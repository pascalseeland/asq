(function ($) {
    const clozeRegex = /{[^}]*}/g;

    function createNewTextGap(i) {
        const template = $('.cloze_template .text').clone();

        template.find('select, input[type=hidden]').each((index, item) => {
            asqAuthoring.processItem($(item), i);
        });

        $(template.find('.aot_table input')).each((index, item) => {
            const input = $(item);
            input.prop('id', input.prop('id').replace('0', i));
            input.prop('name', input.prop('name').replace('0', i));
        });

        return template.children();
    }

    function createNewSelectGap(i) {
        const template = $('.cloze_template .select').clone();

        template.find('select, input[type=hidden]').each((index, item) => {
            asqAuthoring.processItem($(item), i);
        });

        $(template.find('.aot_table input')).each((index, item) => {
            const input = $(item);
            input.prop('id', input.prop('id').replace('0', i));
            input.prop('name', input.prop('name').replace('0', i));
        });

        return template.children();
    }

    function createNewNumberGap(i) {
        const template = $('.cloze_template .number').clone();

        asqAuthoring.processRow(template, i);

        return template.children();
    }

    function addGapItems() {
        const clozeText = $('#cze_text');
        const matches = $('#cze_text').val().match(clozeRegex);
        const gapIndex = matches ? matches.length + 1 : 1;

        const cursor = clozeText[0].selectionStart;
        const text = clozeText.val();
        const beforeCursor = text.substring(0, cursor);
        const afterCursor = text.substring(cursor);
        clozeText.val(`${beforeCursor}{${gapIndex}}${afterCursor}`);

        const lastNonGap = $('#il_prop_cont_cze_text');

        lastNonGap.nextUntil('.ilFormFooter').remove();

        for (let i = 0; i < gapIndex; i += 1) {
            lastNonGap.siblings('.ilFormFooter').before(createNewTextGap(i + 1));
        }
    }

    const nrRegex = /\d*/;

    function changeGapForm() {
        const selected = $(this);
        const id = selected.prop('id').match(nrRegex);
        let template = null;

        if (selected.val() === 'clz_number') {
            template = createNewNumberGap(id);
        } else if (selected.val() === 'clz_text') {
            template = createNewTextGap(id);
        } else if (selected.val() === 'clz_dropdown') {
            template = createNewSelectGap(id);
        }

        const parentItem = selected.parents('.form-group');
        parentItem.nextUntil('.ilFormFooter, .ilFormHeader').remove();
        parentItem.after(template);
        parentItem.next().remove();
        parentItem.next().remove();
    }

    function prepareForm() {
        $('.cloze_template .ilFormFooter').remove();
        const templateForms = $('.cloze_template .form-horizontal');

        templateForms.each((index, item) => {
            const form = $(item);
            form.parent().append(form.children());
            form.remove();
        });
    }

    $(document).ready(prepareForm);

    $(document).on('change', 'select[id$=cze_gap_type]', changeGapForm);
    $(document).on('click', '.js_parse_cloze_question', addGapItems);
}(jQuery));
