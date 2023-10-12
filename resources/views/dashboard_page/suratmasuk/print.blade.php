<!DOCTYPE html>
<html>

<head>
	<title>Cetak Surat Masuk ke Berkas</title>
	<link rel="icon" href="{{url('uploads/'.getSetting('favicon'))}}" type="image/x-icon">
	<style>

		@media print {
			body {
				width: 21cm;
				height: 33cm;
				/*margin: 30mm 45mm 30mm 45mm;*/
				/* change the margins as you want them to be. */
			}
		}

		@media print {
			html, body {
				height: 99%;
			}
		}

		#container {
			width: 21cm;
			height: 33cm;
			background-color: #333;
			display: flex;
			align-items: center;
			justify-content: center;
			overflow: hidden;
			/*border-radius: 7px;*/
			touch-action: none;
		}

		#item {
			width: 100px;
			height: 100px;
			/*background-color: rgb(245, 230, 99);*/
			/*border: 10px solid rgba(136, 136, 136, .5);*/
			/*border-radius: 50%;*/
			touch-action: none;
			user-select: none;
		}

		#item:active {
			background-color: rgba(168, 218, 220, 1.00);
		}

		#item:hover {
			cursor: pointer;
			border-width: 20px;
		}
	</style>

</head>

<body>


<div id="container">

	<img src="{{url('kodeqr/'.$qrcode)}}" id="item">

</div>


</body>
<script>
    var dragItem = document.querySelector("#item");
    var container = document.querySelector("#container");

    var active = false;
    var currentX;
    var currentY;
    var initialX;
    var initialY;
    var xOffset = 0;
    var yOffset = 0;

    container.addEventListener("touchstart", dragStart, false);
    container.addEventListener("touchend", dragEnd, false);
    container.addEventListener("touchmove", drag, false);

    container.addEventListener("mousedown", dragStart, false);
    container.addEventListener("mouseup", dragEnd, false);
    container.addEventListener("mousemove", drag, false);

    function dragStart(e) {
        if (e.type === "touchstart") {
            initialX = e.touches[0].clientX - xOffset;
            initialY = e.touches[0].clientY - yOffset;
        } else {
            initialX = e.clientX - xOffset;
            initialY = e.clientY - yOffset;
        }

        if (e.target === dragItem) {
            active = true;
        }
    }

    function dragEnd(e) {
        initialX = currentX;
        initialY = currentY;

        active = false;
    }

    function drag(e) {
        if (active) {

            e.preventDefault();

            if (e.type === "touchmove") {
                currentX = e.touches[0].clientX - initialX;
                currentY = e.touches[0].clientY - initialY;
            } else {
                currentX = e.clientX - initialX;
                currentY = e.clientY - initialY;
            }

            xOffset = currentX;
            yOffset = currentY;

            setTranslate(currentX, currentY, dragItem);
        }
    }

    function setTranslate(xPos, yPos, el) {
        el.style.transform = "translate3d(" + xPos + "px, " + yPos + "px, 0)";
    }
</script>
<script type="text/javascript">
    window.print();
</script>
</html>

