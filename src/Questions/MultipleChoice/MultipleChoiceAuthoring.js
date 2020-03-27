(function ($) {
    let imageHeader = '';

    function showMultilineEditor() {
        // wait for tiny to load
        if (tinymce.EditorManager.editors.length < 1) {
            setTimeout(showMultilineEditor, 250);
            return;
        }

        const tinySettings = tinymce.EditorManager.editors[0].settings;
        tinySettings.mode = '';
        tinySettings.selector = 'input[id$=mcdd_text]';
        tinymce.init(tinySettings);

        $('input[id$=mcdd_image]').each((index, item) => {
            const td = $(item).parents('td');
            td.children().hide();

            if (imageHeader.length === 0) {
                const th = td.closest('table').find('th').eq(td.index())[0];
                imageHeader = th.innerHTML;
                th.innerHTML = '';
            }
        });
    }

    function hideMultilineEditor() {
        asqAuthoring.clearTiny($('.aot_table tbody'));

        $('input[id$=mcdd_image').each((index, item) => {
            const td = $(item).parents('td');
            td.children().show();

            if (imageHeader.length > 0) {
                const th = td.closest('table').find('th').eq(td.index())[0];
                th.innerHTML = imageHeader;
                imageHeader = '';
            }
        });
    }

    function updateEditor() {
        if (typeof (tinymce) === 'undefined') {
            return;
        }

        if ($('#singleline').val() === 'true') {
            hideMultilineEditor();
        } else {
            showMultilineEditor();
        }
    }

    $(window).load(() => {
        if ($('#singleline').length > 0) {
            updateEditor();
        }
    });

    $(document).on('change', '#singleline', updateEditor);
}(jQuery));
