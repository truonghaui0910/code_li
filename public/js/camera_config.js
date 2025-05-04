$(document).ready(function() {
            const phoneFrame = $('#phone-frame');
            const smallBox = $('#small-box');
            let isDragging = false;
            let isResizing = false;
            let offsetX, offsetY;
            let currentResolution = { width: 1080, height: 1920 };

            // Biến toàn cục để đánh dấu trạng thái của smallBox
            let smallBoxState = {
                x: 0,
                y: 0,
                width: 0,
                height: 0
            };

            const initialData = {
                "smallBox": {
                    "width": 300,
                    "height": 300,
                    "position": {
                        "x": 50,
                        "y": 1060
                    }
                },
                "volume1": "80",
                "volume2": "60"
            };

            function initializeFromData(data) {
                const scaleX = phoneFrame.width() / currentResolution.width;
                const scaleY = phoneFrame.height() / currentResolution.height;
                
                smallBoxState.x = data.smallBox.position.x;
                smallBoxState.y = data.smallBox.position.y;
                smallBoxState.width = data.smallBox.width;
                smallBoxState.height = data.smallBox.height;

                updateSmallBoxPosition(smallBoxState.x * scaleX, smallBoxState.y * scaleY);
                updateSmallBoxSize(smallBoxState.width * scaleX, smallBoxState.height * scaleY);
                
                $('#volume1').val(data.volume1);
                $('#volume2').val(data.volume2);
                
                updateTextBoxes();
            }

            $('#open-modal').click(function() {
                $('#phone-modal').modal('show');
                initializeFromData(initialData);
            });

            smallBox.mousedown(function(e) {
                if ($(e.target).hasClass('resize-handle')) {
                    isResizing = true;
                } else {
                    isDragging = true;
                    offsetX = e.clientX - smallBox.position().left;
                    offsetY = e.clientY - smallBox.position().top;
                }
            });

            $(document).mousemove(function(e) {
                const scaleX = currentResolution.width / phoneFrame.width();
                const scaleY = currentResolution.height / phoneFrame.height();

                if (isDragging) {
                    let left = e.clientX - offsetX;
                    let top = e.clientY - offsetY;
                    updateSmallBoxPosition(left, top);
                    smallBoxState.x = Math.round(left * scaleX);
					
                    smallBoxState.y = Math.round(top * scaleY);
                } else if (isResizing) {
                    let width = e.clientX - smallBox.offset().left;
                    let height = e.clientY - smallBox.offset().top;
                    updateSmallBoxSize(width, height);
                    smallBoxState.width = Math.round(width * scaleX);
                    smallBoxState.height = Math.round(height * scaleY);
                }
                if (isDragging || isResizing) {
                    updateTextBoxes();
                }
            }).mouseup(function() {
                isDragging = false;
                isResizing = false;
            });

            $(document).keydown(function(e) {
                if (smallBox.is(':focus')) {
                    const step = 10;
                    const scaleX = currentResolution.width / phoneFrame.width();
                    const scaleY = currentResolution.height / phoneFrame.height();

                    switch(e.which) {
                        case 37: // left
                            smallBoxState.x -= Math.round(step * scaleX);
                            break;
                        case 38: // up
                            smallBoxState.y -= Math.round(step * scaleY);
                            break;
                        case 39: // right
                            smallBoxState.x += Math.round(step * scaleX);
                            break;
                        case 40: // down
                            smallBoxState.y += Math.round(step * scaleY);
                            break;
                        default: return;
                    }

                    updateSmallBoxPosition(smallBoxState.x / scaleX, smallBoxState.y / scaleY);
                    updateTextBoxes();
                    e.preventDefault();
                }
            });

            function updateSmallBoxPosition(left, top) {
                left = Math.max(0, Math.min(left, phoneFrame.width() - smallBox.width()));
                top = Math.max(0, Math.min(top, phoneFrame.height() - smallBox.height()));
                smallBox.css({left: left + 'px', top: top + 'px'});
            }

            function updateSmallBoxSize(width, height) {
                width = Math.max(50, Math.min(width, phoneFrame.width() - smallBox.position().left));
                height = Math.max(50, Math.min(height, phoneFrame.height() - smallBox.position().top));
                smallBox.css({width: width + 'px', height: height + 'px'});
            }

            function updateTextBoxes() {
                if(smallBoxState.x<0){
                    smallBoxState.x =0;
                }
                if(smallBoxState.x>1080 - smallBoxState.width){
                    smallBoxState.x =1080 - smallBoxState.width;
                }
                if(smallBoxState.y<0){
                    smallBoxState.y =0;
                }
                if(smallBoxState.y>1920 -smallBoxState.height){
                    smallBoxState.y =1920 -smallBoxState.height;
                }
                $('#box-x').val(smallBoxState.x);
                $('#box-y').val(smallBoxState.y);
                $('#box-width').val(smallBoxState.width);
                $('#box-height').val(smallBoxState.height);
            }

            $('.box-controls input').on('input', function() {
                const scaleX = phoneFrame.width() / currentResolution.width;
                const scaleY = phoneFrame.height() / currentResolution.height;
                
                smallBoxState.x = parseInt($('#box-x').val());
                smallBoxState.y = parseInt($('#box-y').val());
                smallBoxState.width = parseInt($('#box-width').val());
                smallBoxState.height = parseInt($('#box-height').val());

                updateSmallBoxPosition(smallBoxState.x * scaleX, smallBoxState.y * scaleY);
                updateSmallBoxSize(smallBoxState.width * scaleX, smallBoxState.height * scaleY);
            });

            $('#resolution-select').change(function() {
                const [width, height] = $(this).val().split('x');
                currentResolution = { width: parseInt(width), height: parseInt(height) };
                const scaleX = phoneFrame.width() / currentResolution.width;
                const scaleY = phoneFrame.height() / currentResolution.height;
                
                updateSmallBoxPosition(smallBoxState.x * scaleX, smallBoxState.y * scaleY);
                updateSmallBoxSize(smallBoxState.width * scaleX, smallBoxState.height * scaleY);
            });

            $('#submit-btn').click(function() {
                const data = {
                    smallBox: {
                        width: smallBoxState.width,
                        height: smallBoxState.height,
                        position: {
                            x: smallBoxState.x,
                            y: smallBoxState.y
                        }
                    },
                    volume1: $('#volume1').val(),
                    volume2: $('#volume2').val()
                };
                console.log(JSON.stringify(data, null, 2));
            });

            smallBox.focus();
        });