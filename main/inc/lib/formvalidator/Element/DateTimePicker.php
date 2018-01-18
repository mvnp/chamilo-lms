<?php
/* For licensing terms, see /license.txt */

/**
 * Form element to select a date and hour.
 */
class DateTimePicker extends HTML_QuickForm_text
{
    /**
     * Constructor
     */
    public function __construct($elementName = null, $elementLabel = null, $attributes = null)
    {
        if (!isset($attributes['id'])) {
            $attributes['id'] = $elementName;
        }
        $attributes['class'] = 'form-control';
        parent::__construct($elementName, $elementLabel, $attributes);
        $this->_appendName = true;
    }

    /**
     * HTML code to display this datepicker
     * @return string
     */
    public function toHtml()
    {
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }

        $id = $this->getAttribute('id');
        $value = $this->getValue();

        if (!empty($value)) {
            $value = api_format_date($value, DATE_TIME_FORMAT_LONG_24H);
        }

        $label = $this->getLabel();
        if (is_array($label) && isset($label[0])) {
            $label = $label[0];
        }

        $resetFieldX = sprintf(get_lang('ResetFieldX'), $label);

        return '
            <div class="input-group">
                <span class="input-group-addon cursor-pointer">
                    <input '.$this->_getAttrString($this->_attributes).'>
                </span>
                <p class="form-control disabled" id="'.$id.'_alt_text">'.$value.'</p>
                <input class="form-control" type="hidden" id="'.$id.'_alt" value="'.$value.'">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button"
                            title="'.$resetFieldX.'">
                        <span class="fa fa-trash text-danger" aria-hidden="true"></span>
                        <span class="sr-only">'.$resetFieldX.'</span>
                    </button>
                </span>
            </div>
        '.$this->getElementJS();
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $value = substr($value, 0, 16);
        $this->updateAttributes(
            [
                'value'=>$value
            ]
        );
    }

    /**
     * Get the necessary javascript for this datepicker
     * @return string
     */
    private function getElementJS()
    {
        $js = null;
        $id = $this->getAttribute('id');
        //timeFormat: 'hh:mm'
        $js .= "<script>
            $(function() {
                var txtDateTime = $('#$id'),
                    inputGroup = txtDateTime.parents('.input-group'),
                    txtDateTimeAlt = $('#{$id}_alt'),
                    txtDateTimeAltText = $('#{$id}_alt_text');

                txtDateTime
                    .hide()
                    .datetimepicker({
                        defaultDate: '".$this->getValue()."',
                        dateFormat: 'yy-mm-dd',
                        timeFormat: 'HH:mm',
                        altField: '#{$id}_alt',
                        altFormat: \"".get_lang('DateFormatLongNoDayJS')."\",
                        altTimeFormat: \"" . get_lang('TimeFormatNoSecJS')."\",
                        altSeparator: \" " . get_lang('AtTime')." \",
                        altFieldTimeOnly: false,
                        showOn: 'both',
                        buttonImage: '" . Display::return_icon('attendance.png', null, [], ICON_SIZE_TINY, true, true)."',
                        buttonImageOnly: true,
                        buttonText: '" . get_lang('SelectDate')."',
                        changeMonth: true,
                        changeYear: true
                    })
                    .on('change', function (e) {
                        txtDateTimeAltText.text(txtDateTimeAlt.val());
                    });
                    
                txtDateTimeAltText.on('click', function () {
                    txtDateTime.datepicker('show');
                });

                inputGroup
                    .find('button')
                    .on('click', function (e) {
                        e.preventDefault();

                        $('#$id, #{$id}_alt').val('');
                        $('#{$id}_alt_text').html('');
                    });
            });
        </script>";

        return $js;
    }

    /**
     * @param string $layout
     *
     * @return string
     */
    public function getTemplate($layout)
    {
        $size = $this->getColumnsSize();
        $value = $this->getValue();
        if (empty($size)) {
            $sizeTemp = $this->getInputSize();
            if (empty($size)) {
                $sizeTemp = 8;
            }
            $size = [2, $sizeTemp, 2];
        } else {
            if (is_array($size)) {
                if (count($size) != 3) {
                    $sizeTemp = $this->getInputSize();
                    if (empty($size)) {
                        $sizeTemp = 8;
                    }
                    $size = [2, $sizeTemp, 2];
                }
                // else just keep the $size array as received
            } else {
                $size = [2, intval($size), 2];
            }
        }

        switch ($layout) {
            case FormValidator::LAYOUT_INLINE:
                return '
                <div class="form-group {error_class}">
                    <label {label-for} >
                        <!-- BEGIN required --><span class="form_required">*</span><!-- END required -->
                        {label}
                    </label>

                    {element}
                </div>';
                break;
            case FormValidator::LAYOUT_HORIZONTAL:
                return '
                <div class="form-group {error_class}">
                    <label {label-for} class="col-sm-'.$size[0].' control-label {extra_label_class}" >
                        <!-- BEGIN required --><span class="form_required">*</span><!-- END required -->
                        {label}
                    </label>
                    <div class="col-sm-'.$size[1].'">
                        {icon}

                        {element}

                        <!-- BEGIN label_2 -->
                            <p class="help-block">{label_2}</p>
                        <!-- END label_2 -->

                        <!-- BEGIN error -->
                            <span class="help-inline help-block">{error}</span>
                        <!-- END error -->
                    </div>
                    <div class="col-sm-'.$size[2].'">
                        <!-- BEGIN label_3 -->
                            {label_3}
                        <!-- END label_3 -->
                    </div>
                </div>';
                break;
            case FormValidator::LAYOUT_BOX_NO_LABEL:
                return '{element}';
                break;
        }
    }
}
