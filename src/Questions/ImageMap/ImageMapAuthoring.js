(function ($) {
    // consts track definitions in ImageMapEditorDisplayDefinition.php
    const TYPE_RECTANGLE = '1';
    const TYPE_CIRCLE = '2';
    const TYPE_POLYGON = '3';

    class Point {
        constructor(X, Y) {
            this.X = X;
            this.Y = Y;
        }
    }

    let popup = null;
    let canvas;
    let previewCanvas;
    let typ;
    let coordinates;
    let label;
    let currentCoordinates = null;
    let polyPoints;
    let start = null;
    let shifted;
    let existingShapes;
    let usedCanvas;

    function recordStart(e) {
        start = new Point(e.offsetX, e.offsetY);
    }

    function transformToPercentage(part, whole) {
        return Math.round((part / whole) * 100);
    }

    function createPoints(stop) {
        const point = {
            topLeft:
                new Point(Math.min(start.X, stop.offsetX),
                    Math.min(start.Y, stop.offsetY)),
            bottomRight:
                new Point(Math.max(start.X, stop.offsetX),
                    Math.max(start.Y, stop.offsetY)),
        };

        if (shifted) {
            const width = point.bottomRight.X - point.topLeft.X;

            // set height to width
            point.bottomRight.Y = point.topLeft.Y + width;
        }

        return point;
    }

    function mapToPreview(value, previewValue) {
        const floatval = parseFloat(value);
        return (floatval / 100.0) * previewValue;
    }

    function mapToPreviewHeight(value) {
        return mapToPreview(value, usedCanvas.height);
    }

    function mapToPreviewWidth(value) {
        return mapToPreview(value, usedCanvas.width);
    }

    const rectangleRegex = /x:([^;]*);y:([^;]*);width:([^;]*);height:([^;]*)/;

    function drawPreviewRectangle(cords, g) {
        const matches = cords.match(rectangleRegex);

        g.beginPath();
        g.rect(mapToPreviewWidth(matches[1]),
            mapToPreviewHeight(matches[2]),
            mapToPreviewWidth(matches[3]),
            mapToPreviewHeight(matches[4]));
        g.fill();
    }

    const circleRegex = /cx:([^;]*);cy:([^;]*);rx:([^;]*);ry:([^;]*)/;

    function drawPreviewCircle(cords, g) {
        const matches = cords.match(circleRegex);

        g.beginPath();
        g.ellipse(mapToPreviewWidth(matches[1]),
            mapToPreviewHeight(matches[2]),
            mapToPreviewWidth(matches[3]),
            mapToPreviewHeight(matches[4]),
            0, 0, 2 * Math.PI);
        g.fill();
    }

    function drawPreviewPolygon(cords, g) {
        const points = cords.substring(7).split(' ');

        g.beginPath();

        const first = points[0].split(',');
        g.moveTo(mapToPreviewWidth(first[0]),
            mapToPreviewHeight(first[1]));

        let i;
        for (i = 1; i < points.length; i += 1) {
            const point = points[i].split(',');
            g.lineTo(mapToPreviewWidth(point[0]),
                mapToPreviewHeight(point[1]));
        }

        g.closePath();

        g.fill();
    }

    function drawShapes(rows, cv) {
        usedCanvas = cv;

        const g = usedCanvas.getContext('2d');
        g.fillStyle = 'rgba(255, 255, 255, 0.8)';

        rows.each((index, item) => {
            const type = $(item).find('select[id$=imedd_type]').val();
            const cords = $(item).find('input[id$=imedd_coordinates]').val();

            if (cords.length > 0) {
                switch (type) {
                case TYPE_RECTANGLE:
                    drawPreviewRectangle(cords, g);
                    break;
                case TYPE_CIRCLE:
                    drawPreviewCircle(cords, g);
                    break;
                case TYPE_POLYGON:
                    drawPreviewPolygon(cords, g);
                    break;
                }
            }
        });
    }

    function drawCircle(origin, destination) {
        const g = canvas[0].getContext('2d');

        g.clearRect(0, 0, canvas.width(), canvas.height());

        drawShapes(existingShapes, canvas[0]);

        g.beginPath();
        g.lineWidth = '3';
        g.strokeStyle = 'black';
        g.ellipse((origin.X + destination.X) / 2,
            (origin.Y + destination.Y) / 2,
            (destination.X - origin.X) / 2,
            (destination.Y - origin.Y) / 2,
            0, 0, 2 * Math.PI);
        g.stroke();

        g.beginPath();
        g.lineWidth = '1';
        g.strokeStyle = 'red';
        g.ellipse((origin.X + destination.X) / 2,
            (origin.Y + destination.Y) / 2,
            (destination.X - origin.X) / 2,
            (destination.Y - origin.Y) / 2,
            0, 0, 2 * Math.PI);
        g.stroke();
    }

    function previewCircle(e) {
        if (start === null) {
            return;
        }

        const points = createPoints(e);

        drawCircle(points.topLeft, points.bottomRight);
    }

    function generateCircle(e) {
        const points = createPoints(e);

        drawCircle(points.topLeft, points.bottomRight);

        currentCoordinates = `cx:${transformToPercentage((points.topLeft.X + points.bottomRight.X) / 2, canvas.width())};`
            + `cy:${transformToPercentage((points.topLeft.Y + points.bottomRight.Y) / 2, canvas.height())};`
            + `rx:${transformToPercentage((points.bottomRight.X - points.topLeft.X) / 2, canvas.width())};`
            + `ry:${transformToPercentage((points.bottomRight.Y - points.topLeft.Y) / 2, canvas.height())}`;

        start = null;
    }

    function drawRectangle(origin, destination) {
        const g = canvas[0].getContext('2d');

        g.clearRect(0, 0, canvas.width(), canvas.height());

        drawShapes(existingShapes, canvas[0]);

        g.beginPath();
        g.lineWidth = '3';
        g.strokeStyle = 'black';
        g.rect(origin.X, origin.Y, destination.X - origin.X, destination.Y - origin.Y);
        g.stroke();

        g.beginPath();
        g.lineWidth = '1';
        g.strokeStyle = 'red';
        g.rect(origin.X, origin.Y, destination.X - origin.X, destination.Y - origin.Y);
        g.stroke();
    }

    function previewRectangle(e) {
        if (start === null) {
            return;
        }

        const points = createPoints(e);

        drawRectangle(points.topLeft, points.bottomRight);
    }

    function generateRectangle(e) {
        const points = createPoints(e);

        drawRectangle(points.topLeft, points.bottomRight);

        currentCoordinates = `x:${transformToPercentage(points.topLeft.X, canvas.width())};`
            + `y:${transformToPercentage(points.topLeft.Y, canvas.height())};`
            + `width:${transformToPercentage(points.bottomRight.X - points.topLeft.X, canvas.width())};`
            + `height:${transformToPercentage(points.bottomRight.Y - points.topLeft.Y, canvas.height())}`;

        start = null;
    }

    function mapPoly(g) {
        if (polyPoints.length < 2) {
            return;
        }

        g.moveTo(polyPoints[0].X, polyPoints[0].Y);

        let i;
        for (i = 1; i < polyPoints.length; i += 1) {
            g.lineTo(polyPoints[i].X, polyPoints[i].Y);
        }

        g.closePath();
    }

    function drawPolygon() {
        const g = canvas[0].getContext('2d');

        g.clearRect(0, 0, canvas.width(), canvas.height());

        drawShapes(existingShapes, canvas[0]);

        g.beginPath();
        g.lineWidth = '3';
        g.strokeStyle = 'black';
        mapPoly(g);
        g.stroke();

        g.beginPath();
        g.lineWidth = '1';
        g.strokeStyle = 'red';
        mapPoly(g);
        g.stroke();
    }

    function generatePolygon(e) {
        if (e.button === 1) {
            currentCoordinates = null;
            polyPoints = [];
            drawPolygon();
            e.preventDefault();
            return;
        }

        if (currentCoordinates === null) {
            currentCoordinates = 'points:';
        }

        currentCoordinates += `${transformToPercentage(e.offsetX, canvas.width())},${
            transformToPercentage(e.offsetY, canvas.height())} `;

        polyPoints.push(new Point(e.offsetX, e.offsetY));

        drawPolygon();
    }

    function displayCoordinateSelector() {
        const image = $('.image_preview').attr('src');

        if (image.length === 0) {
            return;
        }

        $(this).blur();
        shifted = false;
        coordinates = $(this).parents('.aot_row').find('input[id$=imedd_coordinates]');
        typ = $(this).parents('.aot_row').find('select[id$=imedd_type]').val();
        existingShapes = $(this).parents('.aot_row').siblings();
        label = $(this).parents('.aot_row').find('span.imedd_coordinates');
        popup = $('.js_image_popup');
        canvas = $('.js_coordinate_selector_canvas');

        canvas.off();
        switch (typ) {
        case TYPE_RECTANGLE:
            canvas.mousedown(recordStart);
            canvas.mousemove(previewRectangle);
            canvas.mouseup(generateRectangle);
            break;
        case TYPE_CIRCLE:
            canvas.mousedown(recordStart);
            canvas.mousemove(previewCircle);
            canvas.mouseup(generateCircle);
            break;
        case TYPE_POLYGON:
            polyPoints = [];
            currentCoordinates = null;
            canvas.mouseup(generatePolygon);
            break;
        }

        popup.show();

        const img = $('.js_coordinate_selector');
        const imgContent = img.parents('.modal-body');
        img.css('max-width', imgContent.width());
        img.css('max-height', imgContent.height());
        canvas[0].height = img.height();
        canvas[0].width = img.width();
        canvas.css('left', `${(imgContent.width() - img.width()) / 2}px`);
        canvas.css('top', `${(imgContent.height() - img.height()) / 2}px`);
        popup.css('left', `${(window.innerWidth - popup.width()) / 2}px`);
        popup.css('top', `${(window.innerHeight - popup.height()) / 2}px`);

        drawShapes(existingShapes, canvas[0]);
    }

    function closePopup() {
        popup.hide();
    }

    function initializePreview() {
        const previewImage = $('.image_preview');

        if (previewImage.length === 1) {
            previewCanvas = $('<canvas></canvas');
            previewCanvas.css('position', 'absolute');
            previewCanvas[0].width = previewImage.width();
            previewCanvas[0].height = previewImage.height();
            previewCanvas.css('bottom', previewImage.css('marginBottom'));
            previewCanvas.css('left', previewImage.parents('.col-sm-9').css('paddingLeft'));
            previewImage.after(previewCanvas);
        }
    }

    function updatePreview() {
        if (previewCanvas == null) {
            initializePreview();
        }

        const g = previewCanvas[0].getContext('2d');
        g.clearRect(0, 0, previewCanvas.width(), previewCanvas.height());

        drawShapes($('.aot_row'), previewCanvas[0]);
    }

    function submitPopup() {
        if (currentCoordinates !== null) {
            coordinates.val(currentCoordinates);
            label.html(currentCoordinates);
            closePopup();
            updatePreview();
        }
    }

    function processImgKeyUp(e) {
        if (popup === null) {
            return;
        }

        if (e.keyCode === 27) {
            // ESC
            closePopup();
        } else if (e.keyCode === 13) {
            // Enter
            submitPopup();
        } else if (e.keyCode === 17) {
            // ctrl
            shifted = false;
        }
    }

    function processImgKeyDown(e) {
        // ctrl
        if (e.keyCode === 17) {
            shifted = true;
        }
    }

    function clearExistingCoordinates() {
        const row = $(this).closest('tr');
        row.find('.imedd_coordinates').html('');
        row.find('input[id$=imedd_coordinates]').val('');
    }

    $(window).load(() => {
        updatePreview();
    });

    $(document).on('keyup', processImgKeyUp);
    $(document).on('keydown', processImgKeyDown);
    $(document).on('click', '.js_select_coordinates', displayCoordinateSelector);
    $(document).on('click', '.js_image_select', submitPopup);
    $(document).on('click', '.js_image_cancel, .close', closePopup);
    $(document).on('click', '.js_remove', updatePreview);
    $(document).on('change', 'select[id$=imedd_type]', clearExistingCoordinates);
}(jQuery));
