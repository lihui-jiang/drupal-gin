<?php

namespace Drupal\gin;

/**
 * Service to provide color helper functions.
 */
class GinColorHelper {

  /**
   * Prepare css styles string from an array.
   *
   * @param array $styles
   *   An array of css selectors & styles.
   * @param bool $minify
   *   Boolean to indicate if generated css should be minified.
   */
  public function prepareStyles(array $styles, $minify = TRUE) {
    $css = '';
    foreach ($styles as $style_item) {
      $selectors = implode(', ', $style_item['selectors']);
      $css .= "$selectors {\n";
      $style_props = $style_item['styles'];
      foreach ($style_props as $property => $value) {
        $css .= "  $property : $value;\n";
      }
      $css .= "}\n";
    }
    if ($minify) {
      // Remove comments.
      $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
      // Remove spaces after colons.
      $css = str_replace(': ', ':', $css);
      // Remove whitespace.
      $css = preg_replace('/\s+/', ' ', $css);
      // Remove newlines.
      $css = str_replace(["\r\n", "\r", "\n"], '', $css);
    }
    return $css;
  }

  /**
   * Mixes two hexadecimal colors based on a weight.
   *
   * @param string $color_1
   *   The first hexadecimal color without #.
   * @param string $color_2
   *   The second hexadecimal color without #.
   * @param int $weight
   *   The weight of the mix.
   */
  public function mixColor(string $color_1, string $color_2, int $weight = 50) {
    $color = '#';
    for ($i = 0; $i <= 5; $i += 2) {
      $v1 = hexdec(substr($color_1, $i, 2));
      $v2 = hexdec(substr($color_2, $i, 2));
      $val = dechex(floor($v2 + ($v1 - $v2) * ($weight / 100.0)));

      while (strlen($val) < 2) {
        $val = '0' . $val;
      }

      $color .= $val;
    }
    return $color;
  }

  /**
   * Converts a hexadecimal color to rgb.
   *
   * @param string $hex_color
   *   The hexadecimal color to convert.
   */
  public function hexToRgb(string $hex_color) {
    $shorthand_regex = '/^#?([a-f\d])([a-f\d])([a-f\d])$/i';
    $hex = preg_replace_callback($shorthand_regex, function($matches) {
      return $matches[1] . $matches[1] . $matches[2] . $matches[2] . $matches[3] . $matches[3];
    }, $hex_color);

    $result = preg_match('/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i', $hex, $matches);

    if ($result) {
      $rgb = [
        hexdec($matches[1]),
        hexdec($matches[2]),
        hexdec($matches[3]),
      ];
      return implode(', ', $rgb);
    }
    else {
      return NULL;
    }
  }

  /**
   * Shades a hexadecimal color by given percentage.
   *
   * @param string $color
   *   The hexadecimal color.
   * @param float $percent
   *   The percentage by which to shade the color.
   */
  public function shadeColor(string $color, float $percent) {
    $num = hexdec(str_replace('#', '', $color));
    $amt = round(2.55 * $percent);
    $r = (($num >> 16) + $amt) & 0xff;
    $b = (($num >> 8) & 0xff) + $amt;
    $g = ($num & 0xff) + $amt;

    $r = ($r < 255 ? ($r < 1 ? 0 : $r) : 255);
    $b = ($b < 255 ? ($b < 1 ? 0 : $b) : 255);
    $g = ($g < 255 ? ($g < 1 ? 0 : $g) : 255);

    $shaded_color = sprintf("#%02x%02x%02x", $r, $b, $g);
    return $shaded_color;
  }

}
