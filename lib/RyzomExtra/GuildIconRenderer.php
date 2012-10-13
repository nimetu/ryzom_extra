<?php

namespace RyzomExtra;

/**
 * Render guild icon
 */
class GuildIconRenderer
{
    /*
     * [code]
     *     $iconBuilder = new GuildIconBuilder($_GET['icon']);
     *     $icon = new GuildIconRenderer(
     *         $iconBuilder,
     *         '<path-to-guild_icons>'
     *     );
     *     $icon->setSize(64, 64);
     *
     *     header('Content-Type: image/png');
     *     echo $icon->asPng(9);
     *
     * [/code]
     */
    const IMG_COL = 1;
    const IMG_ADD_NEG = 2;
    const IMG_MUL = 3;

    /** @var GuildIconBuilder */
    protected $icon;

    /** @var string */
    protected $size;

    /** @var bool */
    protected $withSymbol;

    /** @var string */
    protected $dataPath;

    /**
     * @param GuildIconBuilder $icon
     * @param string           $dataPath
     */
    public function __construct(GuildIconBuilder $icon, $dataPath)
    {
        $this->icon = $icon;

        $this->dataPath = $dataPath;
        $this->size = 'b';

        $this->withSymbol = true;
    }

    /**
     * Set output image size
     *
     * @param string $size either 'b' for big (64x64) or 's' for small (32x32)
     *                     if invalid, then fall back to big
     */
    public function setSize($size)
    {
        if ($size == 's') {
            $this->size = $size;
        } else {
            $this->size = 'b';
        }
    }

    /**
     * Enable or disable symbol icon on final image
     *
     * @param bool $b
     */
    public function setWithSymbol($b)
    {
        $this->withSymbol = (bool)$b;
    }

    /**
     * Return image as PNG string
     *
     * @param int $compression
     *
     * @return string
     */
    public function asPng($compression = 9)
    {
        ob_start();

        $im = $this->output();
        imagepng($im, null, $compression);

        return ob_get_clean();
    }

    /**
     * Return guild icon as image resource
     *
     * @return resource
     */
    public function output()
    {
        $img_back = sprintf('%s/guild_back_%s_%02d_1.png',
            $this->dataPath,
            $this->size,
            $this->icon->Background
        );

        $img_back2 = sprintf('%s/guild_back_%s_%02d_2.png',
            $this->dataPath,
            $this->size,
            $this->icon->Background
        );

        // colorize backgrounds and join them to one
        $im = imagecreatefrompng($img_back);
        $im_w = imagesx($im);
        $im_h = imagesx($im);
        $im = self::applyFilter(
            $im,
            self::IMG_COL,
            $this->icon->Color1Red,
            $this->icon->Color1Green,
            $this->icon->Color1Blue
        );

        // second background
        $tmp = imagecreatefrompng($img_back2);
        $tmp = $this->applyFilter(
            $tmp,
            self::IMG_COL,
            $this->icon->Color2Red,
            $this->icon->Color2Green,
            $this->icon->Color2Blue
        );
        // join
        imagecopy($im, $tmp, 0, 0, 0, 0, $im_w, $im_h);

        // get the symbol
        if ($this->withSymbol) {
            $img_symbol = sprintf('%s/guild_symbol_%s_%02d.png',
                $this->dataPath,
                $this->size,
                $this->icon->Symbol
            );

            $tmp = imagecreatefrompng($img_symbol);

            if ($this->icon->Inverted == 1) {
                $im = $this->applyFilter($im, self::IMG_ADD_NEG, $tmp);
            } else {
                $im = $this->applyFilter($im, self::IMG_MUL, $tmp);
            }
        }

        return $im;
    }

    /**
     * @param resource $im
     * @param int      $filterType
     * @param mixed    $arg0
     * @param mixed    $arg1
     * @param mixed    $arg2
     *
     * @return resource
     */
    protected function applyFilter($im, $filterType, $arg0 = 0, $arg1 = 0, $arg2 = 0)
    {
        $imgW = imagesx($im);
        $imgH = imagesy($im);

        $out = imagecreatetruecolor($imgW, $imgH);
        imagesavealpha($out, true);
        $trans = imagecolorallocatealpha($out, 0, 0, 0, 127);
        imagefill($out, 0, 0, $trans);
        imagecolortransparent($out, $trans);

        for ($x = 0; $x < $imgW; $x++) {
            for ($y = 0; $y < $imgH; $y++) {
                $rgb = imagecolorat($im, $x, $y);
                $rgba = imagecolorsforindex($im, $rgb);
                $a = $rgba['alpha'];
                $r = $rgba['red'];
                $g = $rgba['green'];
                $b = $rgba['blue'];
                switch ($filterType) {
                    case self::IMG_COL:
                        $r = $arg0;
                        $g = $arg1;
                        $b = $arg2;
                        break;
                    case self::IMG_MUL:
                        $rgba2 = imagecolorsforindex($arg0, imagecolorat($arg0, $x, $y));
                        $r = $r * $rgba2['red'] / 255;
                        $g = $g * $rgba2['green'] / 255;
                        $b = $b * $rgba2['blue'] / 255;
                        break;
                    case self::IMG_ADD_NEG:
                        $rgba2 = imagecolorsforindex($arg0, imagecolorat($arg0, $x, $y));
                        // cap color values to 255
                        $r = min(255, $r + 255 - $rgba2['red']);
                        $g = min(255, $g + 255 - $rgba2['green']);
                        $b = min(255, $b + 255 - $rgba2['blue']);
                        break;
                }
                $val = imagecolorallocatealpha($im, $r, $g, $b, $a);
                imagesetpixel($out, $x, $y, $val);
            }
        }
        return $out;
    }

}

