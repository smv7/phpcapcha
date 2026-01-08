# PHP Captcha Specification

## Overview

`olakunlevpn/phpcapcha` is a PHP 8.0+ library for generating secure, customizable CAPTCHA images using the GD extension. It creates a `GdImage` resource, applies visual manipulations (noise, lines, wave distortion), and outputs the result in PNG format.

## Core Component: `Captcha` Class

**Namespace:** `Olakunlevpn\Captcha`

### Configuration Properties

The class maintains internal state for customization. All properties are modified via chainable setter methods.

| Property           | Type     | Default                  | Description                                                  |
| :----------------- | :------- | :----------------------- | :----------------------------------------------------------- |
| `$fontPath`        | `string` | `null` (Auto-detects)    | Path to `.ttf` file or directory of fonts.                   |
| `$length`          | `int`    | `6`                      | Number of characters (ignored in Math mode).                 |
| `$type`            | `string` | `'mixed'`                | Generator mode: `'mixed'`, `'alpha'`, `'numeric'`, `'math'`. |
| `$textColor`       | `array`  | `[0, 0, 0]`              | RGB array for text.                                          |
| `$backgroundColor` | `array`  | `[255, 255, 255]`        | RGB array for background.                                    |
| `$distortion`      | `array`  | `['enabled' => true...]` | Wave effect configuration.                                   |
| `$noise`           | `array`  | `['enabled' => true...]` | Dot noise configuration.                                     |
| `$lines`           | `array`  | `['enabled' => true...]` | Line noise configuration.                                    |

### API Methods

#### `create(int $width = 200, int $height = 70): self`

**Responsibility:**

1.  Validates font path (throws `Exception` if missing).
2.  Generates the challenge code/text based on `$type`.
3.  Creates a blank GD image of specified dimensions.
4.  Fills background color.
5.  Applies noise (dots) if enabled.
6.  Applies lines if enabled.
7.  Renders text with random angles and offsets.
8.  Applies wave distortion if enabled.

#### `output(): void`

**Responsibility:**

- Sends HTTP headers (`Content-Type: image/png`, `Cache-Control: no-store`).
- Outputs the image binary stream.
- Destroys the image resource to free memory.

#### `getBase64(): string`

**Returns:** A Data URI string (e.g., `data:image/png;base64,...`).

#### `save(string $path): bool`

**Responsibility:** Writes the generated image to the file system.

#### `getCode(): string`

**Returns:** The valid answer for the current captcha (e.g., `"8"` for a math problem `"5 + 3"`).

## Internal Logic

### Math Mode

- **Logic:** Generates two random integers (1-9) and an operator (currently defaults to `+`).
- **Display:** Renders `"5 + 3"`.
- **Code:** Stores `"8"`.

### Font Randomization

- If a directory path is passed to `setFont()`, the library scans for `*.ttf` files and randomly selects one for the current instance.

### Wave Distortion

- **Algorithm:** Sinusoidal wave mapping.
- **Formula:** `y_new = y + amplitude * sin(x / period)`
- **Effect:** shifts pixels vertically based on horizontal position to create a ripple effect.
