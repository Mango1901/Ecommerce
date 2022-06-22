<?php

namespace Magenest\Promobar\Block\Adminhtml\Grid\Renderer;

class PreviewButton extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Prepare link to display in grid
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getData('button_id')) {
            $style = " ";
            $editButton = json_decode($row->getData('edit_button'), true);
            $height = $editButton['height'] . 'px';
            $width = $editButton['width'] . 'px';
            $border = $editButton['border'];
            $borderTopLeft = $editButton['top_left'];
            $borderTopRight = $editButton['top_right'];
            $borderBottomLeft = $editButton['bottom_left'];
            $borderBottomRight = $editButton['bottom_right'];
            if (isset($editButton['padding_top'])) {
                $paddingTop = $editButton['padding_top'];
                $style .= " top:0; padding-top: " . $paddingTop . "px;";
            };
            if (isset($editButton['padding_bottom'])) {
                $paddingBottom = $editButton['padding_bottom'];
                $style .= " bottom:0; padding-bottom: " . $paddingBottom . "px;";
            };
            if (isset($editButton['padding_right'])) {
                $paddingRight = $editButton['padding_right'];
                $style .= " right:0; padding-right: " . $paddingRight . "px;";
            };
            if (isset($editButton['padding_left'])) {
                $paddingLeft = $editButton['padding_left'];
                $style .= " left:0; padding-left: " . $paddingLeft . "px;";
            };
            if (($borderTopLeft + $borderTopRight + $borderBottomRight + $borderBottomLeft) > 0) {
                $borderRadius = $borderTopLeft . 'px' . ' ' . $borderTopRight . 'px' . ' ' . $borderBottomRight . 'px' . ' ' . $borderBottomLeft . 'px';
            } else {
                $borderRadius = $border . 'px';
            }

            $html = '<div class="area-button-preview" id="area-button-preview">' .
                '<a id="button-preview" href="" style="position:relative; display: inline-block;line-height:1.3;' .
                'text-decoration: none; height: ' . $height . '; width: ' . $width . '  ;' .
                'border-radius: ' . $borderRadius . ';' .
                'font-size: ' . $row->getData('size') . 'px;' .
                'color:'.$row->getData('text_color').';'.
                'background-color: ' . $row->getData('background_color') . ';' .
                'border-width: ' . $row->getData('border_width') . 'px;' .
                'border-style: ' . $row->getData('border_style') . ';' .
                'border-color: ' . $row->getData('background_color_border') . ';">' .
                '<div class="dv-content-button" style="position:absolute; '.$style.'">' . $row->getData('content') . '</div></a>' .
                '</div>';
        } else {
            $html = '';
        }
        return $html;
    }
}
