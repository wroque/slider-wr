<div id="frame-box"></div>

<div id="tab1" class="tab_content" style="display: block;">

    <fieldset>
        <legend><?php echo __('Add Slider'); ?></legend>
        <form method="POST" enctype="multipart/form-data" id="formSlider">
            <input type="hidden" name="slider[ID]" value="">
            <div class="row">
                <div class="col2">
                    <div class="form-item">
                        <label><?php echo __('Name'); ?></label>
                        <input type="text" name="slider[slider_name]" size="25"/>
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Min'); ?></label>
                        <input type="text" name="slider[slider_atts][minSlides]" value="" />
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Max'); ?></label>
                        <input type="text" name="slider[slider_atts][maxSlides]" value="" />
                    </div>
                </div>

                <div class="col2">
                    <div class="form-item">
                        <label><?php echo __('Move'); ?></label>
                        <input type="text" name="slider[slider_atts][moveSlides]" value="" />
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Width'); ?></label>
                        <input type="text" name="slider[slider_atts][slideWidth]" value="" />
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Margin'); ?></label>
                        <input type="text" name="slider[slider_atts][slideMargin]" value="" />
                    </div>
                </div>
                <div class="col2">
                    <div class="form-item">
                        <label><?php echo __('Mode'); ?></label>
                        <select name="slider[slider_atts][mode]">
                            <option value="horizontal">horizontal</option>
                            <option value="vertical">vertical</option>
                        </select>
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Auto'); ?></label>
                        <input type="checkbox" name="slider[slider_atts][auto]" value="true" />
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Pager'); ?></label>
                        <input type="checkbox" name="slider[slider_atts][pager]" value="true" />
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Controls'); ?></label>
                        <input type="checkbox" name="slider[slider_atts][controls]" value="true" />
                    </div>
                    <div class="form-item">
                        <label><?php echo __('AutoControls'); ?></label>
                        <input type="checkbox" name="slider[slider_atts][autoControls]" value="true" />
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Random Start'); ?></label>
                        <input type="checkbox" name="slider[slider_atts][randomStart]" value="true" />
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Infinite Loop'); ?></label>
                        <input type="checkbox" name="slider[slider_atts][infiniteLoop]" value="true" />
                    </div>
                </div>
            </div>
            <div class="form-options">
                <label>&nbsp;</label>
                <button type="submit" class="button button-primary button-large"><b><?php echo __('Save'); ?></b></button>
                <button type="reset" class="button"><?php echo __('Clear'); ?></button>
            </div>
        </form>

    </fieldset>

    <table class="wp-list-table widefat fixed">
        <thead>
            <tr>
                <th><?php echo __('Name'); ?></th>
                <th><?php echo __('N Items'); ?></th>
                <th><?php echo __('Date'); ?></th>
                <th width="140"><?php echo __('Panel'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sliders as $item): ?>
                <tr data-id="<?php echo $item->ID; ?>">
                    <td><?php echo $item->slider_name; ?></td>
                    <td><?php echo $item->cant; ?></td>
                    <td><?php echo date('d-m-Y H:i:s', strtotime($item->created)); ?></td>
                    <td>
                        <a href="<?php echo admin_url('admin-ajax.php'); ?>" data-slider-option="edit"><?php echo __('Edit'); ?></a> &nbsp;
                        <a href="<?php echo admin_url('admin-ajax.php'); ?>" data-slider-option="delete"><?php echo __('Remove'); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">

    jQuery(document).ready(function() {

        jQuery('#tabs li').click(function(event) {
            jQuery('#tabs li').removeClass('active');
            jQuery(this).addClass('active');
            jQuery('.tab_content').hide();
            var selected_tab = jQuery(this).find('a').attr('href');
            jQuery(selected_tab).show();
            event.preventDefault();
        });

        jQuery('[data-slider-option]').click(function(event) {
            var elem = jQuery(this);
            jQuery.ajax({
                async: false,
                url: elem.attr('href'),
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'slider_wr_ajax',
                    option: elem.data('slider-option'),
                    ID: elem.parents('tr').data('id')
                },
                beforeSend: function() {
                    jQuery('[type="reset"]').trigger('click');
                    event.preventDefault();
                }
            }).done(function(data) {
                if (typeof data === 'object') {
                    var atts = JSON.parse(data.slider_atts);
                    for (var i in atts) {
                        var name = '[name="slider[slider_atts][' + i + ']"]';
                        switch (jQuery(name).attr('type')) {
                            case 'text':
                                jQuery(name).val(atts[i]);
                                break;
                            case 'checkbox':
                                if (atts[i]) {
                                    jQuery(name).attr('checked', true);
                                }
                                break;
                            default:
                                jQuery('[value="' + atts[i] + '"]').attr('selected', 'selected');
                                break;
                        }
                    }
                    delete data.slider_atts;
                    for (var i in data) {
                        jQuery('[name="slider[' + i + ']"]').val(data[i]);
                    }
                    jQuery.post(elem.attr('href'), {
                        action: 'slider_wr_ajax',
                        option: 'get',
                        ID: elem.parents('tr').data('id')
                    }, function(data) {
                        jQuery('#frame-box').html(data);
                    })
                } else {
                    jQuery('[data-id=' + data + ']').remove();
                    jQuery('[type="reset"]').trigger('click');
                    jQuery('#frame-box').html(null);
                }
            });
        });

        jQuery('[type="reset"]').click(function() {
            jQuery('[type="checkbox"]').removeAttr('checked');
            jQuery('#frame-box').html(null);
        });

    });

    elita.validate({
        form: '#formSlider',
        alert: '#alert',
        data: {
            'slider[slider_name]': 'required'
        }
    });

</script>
