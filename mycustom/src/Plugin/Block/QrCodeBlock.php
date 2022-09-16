<?php

namespace Drupal\mycustom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Provides a 'Custom QR code block' Block.
 *
 * @Block(
 *   id = "mycustom",
 *   admin_label = @Translation("Products QR Code Block"),
 *   category = @Translation("QR Code Block"),
 * )
 */

class QrCodeBlock extends BlockBase {

    /**
     * {@inheritdoc}
    */
    
    public function build() {

        $node = \Drupal::routeMatch()->getParameter('node');
    
        if ($node instanceof \Drupal\node\NodeInterface) {
            // You can get nid and anything else you need from the node object.
            $nid = $node->id();

            if($node->bundle() == 'products') {
                //get the product url
                $product_url = $node->field_product_link->uri;
                $directory = "public://";

                //Generating QR Code
                $result = Builder::create()
                ->writer(new PngWriter())
                ->writerOptions([])
                ->data($product_url)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->size(300)
                ->margin(10)
                ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
                ->labelText('Scan here on your mobile')
                ->labelFont(new NotoSans(20))
                ->labelAlignment(new LabelAlignmentCenter())
                ->build();

                // Save it to a file
                $result->saveToFile($directory.'/qrcode'.$nid.'.png');

                // Generate a data URI to include image data inline (i.e. inside an <img> tag)
                $dataUri = $result->getDataUri();
                
                return [
                    '#markup' => '
                    <p>To Purchase this product on our app to avail exclusive app-only</p><br>
                    <img src="/sites/default/files/qrcode'.$nid.'.png" alt="qrcode" />',
                ];
            }    
        }   
    }
  
  }