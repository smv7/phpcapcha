<?php

namespace Olakunlevpn\Captcha;

use Exception;
use GdImage;

class Captcha
{
    protected string $fontPath;
    protected int $length = 6;
    protected string $type = 'mixed'; // 'mixed', 'alpha', 'numeric', 'math'
    
    // Configuration properties
    protected array $textColor = [0, 0, 0];
    protected array $backgroundColor = [255, 255, 255];
    protected array $distortion = ['enabled' => true, 'amplitude' => 0, 'period' => 0];
    protected array $noise = ['enabled' => true, 'count' => 50];
    protected array $lines = ['enabled' => true, 'count' => 3];

    // Internal state
    protected string $code = ''; // The value to validate (e.g. "8")
    protected string $text = ''; // The text to draw (e.g. "5 + 3")
    protected GdImage $image;

    public function __construct(array $config = [])
    {
        if (isset($config['font'])) $this->setFont($config['font']);
        if (isset($config['length'])) $this->setLength($config['length']);
        if (isset($config['type'])) $this->setType($config['type']);
    }

    public function setFont(string $path): self
    {
        if (is_dir($path)) {
            $fonts = glob(rtrim($path, '/') . '/*.ttf');
            if (empty($fonts)) {
                throw new Exception("No .ttf fonts found in directory: $path");
            }
            $path = $fonts[array_rand($fonts)];
        }

        if (!file_exists($path)) {
            throw new Exception("Font file not found: $path");
        }
        $this->fontPath = $path;
        return $this;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;
        return $this;
    }

    public function setType(string $type): self
    {
        $validTypes = ['mixed', 'alpha', 'numeric', 'math'];
        if (!in_array($type, $validTypes)) {
            throw new Exception("Invalid captcha type. Allowed: " . implode(', ', $validTypes));
        }
        $this->type = $type;
        return $this;
    }

    public function setTextColor(int $r, int $g, int $b): self
    {
        $this->textColor = [$r, $g, $b];
        return $this;
    }

    public function setBackgroundColor(int $r, int $g, int $b): self
    {
        $this->backgroundColor = [$r, $g, $b];
        return $this;
    }

    // Advanced Configuration
    public function setDistortion(bool $enabled, int $amplitude = 0, int $period = 0): self
    {
        $this->distortion = [
            'enabled' => $enabled,
            'amplitude' => $amplitude,
            'period' => $period
        ];
        return $this;
    }

    public function setNoise(bool $enabled, int $count = 50): self
    {
        $this->noise = ['enabled' => $enabled, 'count' => $count];
        return $this;
    }

    public function setLines(bool $enabled, int $count = 3): self
    {
        $this->lines = ['enabled' => $enabled, 'count' => $count];
        return $this;
    }

    protected function generateCode(): void
    {
        if ($this->type === 'math') {
            $num1 = random_int(1, 9);
            $num2 = random_int(1, 9);
            $ops = ['+', '-', '*'];
            $op = $ops[array_rand($ops)];
            
            // Simplify for standard usage (stick to + and - for easier UX, or keep *)
            // Let's stick to + for now to keep it simple, or randomization:
            $op = '+'; 
            
            $this->text = "$num1 $op $num2";
            $this->code = (string)($num1 + $num2);
            return;
        }

        $characters = '';
        if ($this->type === 'numeric' || $this->type === 'mixed') {
            $characters .= '23456789';
        }
        if ($this->type === 'alpha' || $this->type === 'mixed') {
            $characters .= 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
        }

        $code = '';
        $max = strlen($characters) - 1;
        for ($i = 0; $i < $this->length; $i++) {
            $code .= $characters[random_int(0, $max)];
        }
        $this->code = $code;
        $this->text = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function create(int $width = 200, int $height = 70): self
    {
        if (empty($this->fontPath)) {
            // Default font fallback
            if (file_exists(__DIR__ . '/../../fonts/Monaco.ttf')) {
                $this->fontPath = realpath(__DIR__ . '/../../fonts/Monaco.ttf');
            } else {
                 throw new Exception("Font path is not set and default font not found.");
            }
        }

        $this->generateCode();

        $this->image = imagecreatetruecolor($width, $height);
        
        $bgColor = imagecolorallocate($this->image, ...$this->backgroundColor);
        imagefilledrectangle($this->image, 0, 0, $width, $height, $bgColor);

        $textColor = imagecolorallocate($this->image, ...$this->textColor);

        // Add Noise
        if ($this->noise['enabled']) {
            for ($i = 0; $i < $this->noise['count']; $i++) {
                $noiseColor = imagecolorallocate($this->image, mt_rand(150, 255), mt_rand(150, 255), mt_rand(150, 255));
                if ($this->backgroundColor[0] > 200) {
                    $noiseColor = imagecolorallocate($this->image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
                }
                imagefilledellipse($this->image, mt_rand(0, $width), mt_rand(0, $height), 2, 2, $noiseColor);
            }
        }

        // Add Lines
        if ($this->lines['enabled']) {
            for ($i = 0; $i < $this->lines['count']; $i++) {
                $lineColor = imagecolorallocate($this->image, mt_rand(100, 200), mt_rand(100, 200), mt_rand(100, 200));
                imageline($this->image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $lineColor);
            }
        }

        // Calculate Font Size
        $fontSize = $height * 0.45;
        // Adjust font size for math equations if they are long
        if ($this->type === 'math') $fontSize = $height * 0.40;

        // Render Text
        $len = strlen($this->text);
        $paddingX = 20;
        $availWidth = $width - ($paddingX * 2);
        $charStep = $availWidth / $len;
        
        $y = ($height / 2) + ($fontSize / 2) - 5; 

        for ($i = 0; $i < $len; $i++) {
            $angle = mt_rand(-20, 20);
            $x = $paddingX + ($i * $charStep);
            $charY = $y + mt_rand(-5, 5);

            imagettftext($this->image, $fontSize, $angle, (int)$x, (int)$charY, $textColor, $this->fontPath, $this->text[$i]);
        }

        // Distortion
        if ($this->distortion['enabled']) {
            $this->applyWave($width, $height);
        }

        return $this;
    }

    protected function applyWave($width, $height)
    {
        $img2 = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($img2, ...$this->backgroundColor);
        imagefilledrectangle($img2, 0, 0, $width, $height, $bgColor);
        
        // Use custom or random parameters
        $period = $this->distortion['period'] ?: mt_rand(30, 50); 
        $amplitude = $this->distortion['amplitude'] ?: mt_rand(3, 6);
        
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $xo = $x;
                $yo = $y + $amplitude * sin($x / $period);
                
                if ($yo >= 0 && $yo < $height) {
                    $rgb = imagecolorat($this->image, $x, (int)$yo);
                    imagesetpixel($img2, $x, $y, $rgb);
                }
            }
        }
        
        $this->image = $img2;
    }

    public function output(): void
    {
        if (!isset($this->image)) throw new Exception("Image not created.");
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Content-Type: image/png');
        imagepng($this->image);
    }
    
    public function getBase64(): string 
    {
        if (!isset($this->image)) throw new Exception("Image not created.");
        ob_start();
        imagepng($this->image);
        $data = ob_get_clean();
        return 'data:image/png;base64,' . base64_encode($data);
    }

    public function save(string $path): bool
    {
        if (!isset($this->image)) throw new Exception("Image not created.");
        $result = imagepng($this->image, $path);
        return $result;
    }
}