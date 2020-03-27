(function ($) {
    const MATCHING_ONE_TO_ONE = '0';
    const MATCHING_MANY_TO_ONE = '1';
    const MATCHING_MANY_TO_MANY = '2';
    let matchingMode;

    let usedTerms = [];
    let usedDefinitions = [];

    function updateValues(source, destination, useds) {
        const values = {};

        let i = 0;
        $(`input[id$="${source}"]`).each((index, item) => {
            const val = $(item).val();

            if (!useds.includes(i.toString())) {
                values[i] = val;
            }

            i += 1;
        });

        $(`select[id$="${destination}"]`).each((index, item) => {
            const that = $(item);
            const selectedVal = that.val();
            const selectedText = that.children('option:selected').text();
            that.empty();

            Object.keys(values).forEach((key) => {
                that.append(new Option(values[key], key));
            });

            if (useds.includes(selectedVal)) {
                let found = false;

                that.children().each((ix, child) => {
                    const childVal = parseInt($(child).val(), 10);

                    if (childVal > parseInt(selectedVal, 10) && !found) {
                        $(child).before(new Option(selectedText, selectedVal));
                        found = true;
                    }
                });

                if (!found) {
                    that.append(new Option(selectedText, selectedVal));
                }
            }

            that.val(selectedVal);
        });
    }

    function updateDefinitions() {
        updateValues('me_definition_text', 'me_match_definition',
            usedDefinitions);
    }

    function updateTerms() {
        updateValues('me_term_text', 'me_match_term', usedTerms);
    }

    function updateUsed(selects, useds) {
        useds.splice(0, useds.length);

        $(`select[id$="${selects}"]`).each((index, item) => {
            const val = $(item).val();
            if (val !== null) {
                useds.push(val);
            }
        });
    }


    function updateUsedDefinitions() {
        if (matchingMode === MATCHING_ONE_TO_ONE) {
            updateUsed('me_match_definition', usedDefinitions);
        } else {
            usedDefinitions = [];
        }

        updateValues('me_definition_text', 'me_match_definition',
            usedDefinitions);
    }

    function updateUsedTerms() {
        if (matchingMode === MATCHING_ONE_TO_ONE
                || matchingMode === MATCHING_MANY_TO_ONE) {
            updateUsed('me_match_term', usedTerms);
        } else {
            usedTerms = [];
        }

        updateValues('me_term_text', 'me_match_term', usedTerms);
    }

    function cleanAddedRow() {
        $('#il_prop_cont_me_matches').find('tr').last().find('select')
            .each((index, item) => {
                $(item).empty();
            });

        updateDefinitions();
        updateTerms();
    }

    $(document).ready(() => {
        if ($('input[name=me_matching]').length > 0) {
            matchingMode = $('input[name=me_matching]:checked').val();
            updateUsedDefinitions();
            updateUsedTerms();
        }
    });

    $(document).on('change', 'input[name=me_matching]', () => {
        matchingMode = $(this).val();
        updateUsedDefinitions();
        updateUsedTerms();
    });

    $(document).on('change', 'input[id$="me_definition_text"]',
        updateDefinitions);
    $(document).on('change', 'input[id$="me_term_text"]', updateTerms);
    $(document).on('change', 'select[id$=me_match_definition]',
        updateUsedDefinitions);
    $(document).on('change', 'select[id$=me_match_term]', updateUsedTerms);

    // remove/add needs to trigger after remove event that actually removes the row
    $(document).on('click', '#il_prop_cont_me_matches .js_add', () => {
        setTimeout(cleanAddedRow, 1);
    });
    $(document).on('click', '#il_prop_cont_me_terms .js_remove', () => {
        setTimeout(updateTerms, 1);
    });
    $(document).on('click', '#il_prop_cont_me_definitions .js_remove',
        () => {
            setTimeout(updateDefinitions, 1);
        });
}(jQuery));
