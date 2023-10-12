<!doctype html>
<head>
    <style>
        body {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        #selectionMarquee {
            z-position: 1000;
            position: absolute;
            border: 1px dashed #ccc;
        }

        .selected-box {
            z-position: 999;
            position: absolute;
            border: 1px dotted #333;
        }

        #all-cords {
            position: fixed;
            right: 0;
            bottom: 0;
            background: #9f9;
        }

        #the-canvas {
          border: 1px solid black;
          direction: ltr;
        }
    </style>
</head>
<p>Click and drag to create coordinate selections</p>
<canvas id="the-canvas"></canvas>
<div id='selectionMarquee'></div>
<ol id='all-cords'></ol>
<script src="{{assetku('assets/modules/jquery.min.js')}}"></script>
<script src="{{assetku('assets/js/pdfjs/build/pdf.js')}}"></script>
<script>

    // If absolute URL from the remote server is provided, configure the CORS
    // header on that server.
    var url = 'https://raw.githubusercontent.com/mozilla/pdf.js/ba2edeae/examples/learning/helloworld.pdf';

    // Loaded via <script> tag, create shortcut to access PDF.js exports.
    var pdfjsLib = window['pdfjs-dist/build/pdf'];

    // The workerSrc property shall be specified.
    pdfjsLib.GlobalWorkerOptions.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

    // Asynchronous download of PDF
    var loadingTask = pdfjsLib.getDocument(url);
    loadingTask.promise.then(function(pdf) {
      console.log('PDF loaded');

      // Fetch the first page
      var pageNumber = 1;
      pdf.getPage(pageNumber).then(function(page) {
        console.log('Page loaded');

        var scale = 1.5;
        var viewport = page.getViewport({scale: scale});

        // Prepare canvas using PDF page dimensions
        var canvas = document.getElementById('the-canvas');
        var context = canvas.getContext('2d');
        canvas.height = viewport.height;
        canvas.width = viewport.width;

        // Render PDF page into canvas context
        var renderContext = {
          canvasContext: context,
          viewport: viewport
        };
        var renderTask = page.render(renderContext);
        renderTask.promise.then(function () {
          console.log('Page rendered');
        });
      });
    }, function (reason) {
      // PDF loading error
      console.error(reason);
    });

    $(document).ready(function() {
        "use strict";
        var startX,
            startY,
            selectedBoxes = [],
            $selectionMarquee = $('#selectionMarquee'),
            $allCords = $('#all-cords'),
            positionBox = function ($box, coordinates) {
                $box.css(
                    'top', coordinates.top
                ).css(
                    'left', coordinates.left
                ).css(
                    'height', coordinates.bottom - coordinates.top
                ).css(
                    'width', coordinates.right - coordinates.left
                );
            },

            compareNumbers = function (a, b) {
                return a - b;
            },
            getBoxCoordinates = function (startX, startY, endX, endY) {
                var x = [startX, endX].sort(compareNumbers),
                    y = [startY, endY].sort(compareNumbers);

                return {
                    top: y[0],
                    left: x[0],
                    right: x[1],
                    bottom: y[1]
                };
            },
            trackMouse = function (event) {
                var position = getBoxCoordinates(startX, startY, event.pageX, event.pageY);
                console.log(position);
                positionBox($selectionMarquee, position);
            },
            displayCoordinates = function () {
                var msg = 'Boxes so far:\n';

                selectedBoxes.forEach(function (box) {
                    msg += '<li>(' + box.left + ', ' + box.top + ') - (' + (box.left + box.right) + ', ' + (box.top + box.bottom) + ')</li>';
                });
                $allCords.html(msg);
            };


        $(document).on('mousedown', function (event) {
            startX = event.pageX;
            startY = event.pageY;
            positionBox($selectionMarquee, getBoxCoordinates(startX, startY, startX, startY));
            $selectionMarquee.show();
            $(this).on('mousemove', trackMouse);
        }).on('mouseup', function (event) {
            var position,
                $selectedBox;

            $selectionMarquee.hide();

            position = getBoxCoordinates(startX, startY, event.pageX, event.pageY);

            if (position.left !== position.right && position.top !== position.bottom) {
                $selectedBox = $('<div class="selected-box"></div>');
                $selectedBox.hide();
                $('body').append($selectedBox);

                positionBox($selectedBox, position);

                $selectedBox.show();

                selectedBoxes.push(position);
                displayCoordinates();
                $(this).off('mousemove', trackMouse);
            }
        });
    });
</script>
