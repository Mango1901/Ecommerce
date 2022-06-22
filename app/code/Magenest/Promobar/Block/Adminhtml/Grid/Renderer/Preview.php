<?php
/**
 * Sitemap grid link column renderer
 *
 */
namespace Magenest\Promobar\Block\Adminhtml\Grid\Renderer;

class Preview extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Prepare link to display in grid
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {

		$html = '<div style="max-width:450px"><div class="promobar'.$this->getCustomClass($row).'">';
		$html .= '<a><img alt="" src="'.$this->getBarImageUrl($row).'" class="img-responsive" /></a>';
		
		if(($row->getContent() != '') || ($row->getButton() != '')){
			$html .= '<div class="text '.$row->getTextAlign().'">';
			
			if($row->getContent() != ''){
				$html .= '<div class="bar-text">'.$row->getContent().'</div>';
			}
			if($row->getButton() != ''){
				$html .= '<span class="bar-button"><button class="btn btn-default btn-promo-bar">'.$row->getButton().'</button></span>';
			}
			$html .= '</div>';
		}

		$html .= '</div></div>';
		return $html;
    }
	
	public function getBarImageUrl($bar){
		$barUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . 'promobar/'.$bar->getFilename();
		return $barUrl;
	}

}
