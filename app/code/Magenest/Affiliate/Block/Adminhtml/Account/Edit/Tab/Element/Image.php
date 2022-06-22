<?php
namespace Magenest\Affiliate\Block\Adminhtml\Account\Edit\Tab\Element;

use Magento\Framework\UrlInterface;

class Image extends \Magento\Framework\Data\Form\Element\Image
{
    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $require = $this->getRequired() ? "required" : "";

        $url = $this->_getUrl();
        if (!empty($url) && !preg_match("/^http\:\/\/|https\:\/\//", $url)) {
            $url = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]). "affiliate/" . $url;
        }
        $source = !empty($url) ? 'src="' . $url . '"' : '';
        $elementId = $this->getData('html_id');

        $html = '
<div class="col-md-10">
    <div>
        <img id="' . $elementId .'_preview" width="100%" style="border:solid" ' . $source . '/>
    </div>
    <div>
        <input class="affiliate_image" type="file" title="search image"
               id="' . $elementId .'" name="' . $elementId .'" ' . $require . ' />
    </div>
</div>';

        $html .= '
<script>
    require(["jquery", "Magento_Ui/js/modal/alert"], function ($, alert) {
        $("#' . $elementId . '").change(function () {
            let validExtensions = ["jpg", "jpeg", "png"],
                fileName = this.files[0].name,
                fileNameExt = fileName.substr(fileName.lastIndexOf(".") + 1);
            if ($.inArray(fileNameExt, validExtensions) === -1) {
                this.type = "";
                this.type = "file";
                $("#" + this.id + "_preview").attr("src","");
                alert({
                    title: $.mage.__("Error"),
                    content: $.mage.__("Only these file types are accepted : " + validExtensions.join(", "))
                });
            } else if (this.files && this.files[0]) {
                let filerdr = new FileReader(), elementId = this.id;
                filerdr.onload = function (e) {
                    $("#" + elementId + "_preview").attr("src", e.target.result);
                }
                filerdr.readAsDataURL(this.files[0]);
            }
        })
    })
</script>';
        $this->setClass('input-file');

        return $html;
    }
}
