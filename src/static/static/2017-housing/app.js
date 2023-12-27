var classyear, blocksize, chemfree = null;
    /**
     * A multidimensional object?
     * Dimension 1: SVG Path ID
     * Dimension 2: Class year
     * Dimension 3: 
     * @type {Object}
     */
    
    /* 1: deep blue, 2: blue, 3: yellow, 4: orange, 5: red */
    var colors = ["", "#262262", "#1B75BC", "#D7DF23", "#FBB040", "#8C4646"];
    
    /* 1: not likely, 3: toss-up, 5: very likely */
    var colorData = {
        'true': {
            'quint': {
                'sophomore': {
                    "HOWARDHALL": colors[4]
                },
                'junior': {
                    "HOWARDHALL": colors[5]
                },
                'senior': {
                    "HOWARDHALL": colors[5]
                }
            },
            'quad': {
                'sophomore': {
                    "HOWARDHALL": colors[3],
                    "_52HARPSWELL": colors[3] 
                },
                'junior': {
                    "HOWARDHALL": colors[5],
                    "_52HARPSWELL": colors[4]
                },
                'senior': {
                    "HOWARDHALL": colors[5],
                    "_52HARPSWELL": colors[5]
                }
            },
            'triple': {
                'sophomore': {
                    "MAYFLOWERAPTS": colors[5]
                },
                'junior': {
                    "MAYFLOWERAPTS": colors[5]
                },
                'senior': {
                    "MAYFLOWERAPTS": colors[5]
                }
            },
            'double': {
                'sophomore': {
                    "SMITHHOUSE": colors[1],
                    "MAYFLOWERAPTS": colors[3],
                    "_52HARPSWELL": colors[4],
                },
                'junior': {
                    "SMITHHOUSE": colors[3],
                    "MAYFLOWERAPTS": colors[5],
                    "_52HARPSWELL": colors[5],
                },
                'senior': {
                    "SMITHHOUSE": colors[4],
                    "MAYFLOWERAPTS": colors[5],
                    "_52HARPSWELL": colors[5],
                }
            },
            'single': {
                'sophomore': {
                    "SMITHHOUSE": colors[2],
                    "_52HARPSWELL": colors[2],
                },
                'junior': {
                    "SMITHHOUSE": colors[4],
                    "_52HARPSWELL": colors[5],
                },
                'senior': {
                    "SMITHHOUSE": colors[5],
                    "_52HARPSWELL": colors[5],
                }
            },
        },
        'false': {
            'quint': {
                'sophomore': {
                    "STOWEHALL": colors[4]
                },
                'junior': {
                    "STOWEHALL": colors[5]
                },
                'senior': {
                    "STOWEHALL": colors[5]
                }
            },
            'quad': {
                'sophomore': {
                    "COLESTOWER": colors[1],
                    "CHAMBERLAIN": colors[1],
                    "HARPSWELLAPTS": colors[2],
                    "PINEAPTS": colors[3],
                },
                'junior': {
                    "COLESTOWER": colors[2],
                    "CHAMBERLAIN": colors[1],
                    "HARPSWELLAPTS": colors[4],
                    "PINEAPTS": colors[5],
                },
                'senior': {
                    "COLESTOWER": colors[4],
                    "CHAMBERLAIN": colors[4],
                    "HARPSWELLAPTS": colors[5],
                    "PINEAPTS": colors[5],
                }
            },
            'triple': {
                'sophomore': {
                    "BRUNSWICKAPTS": colors[3],
                    "COLESTOWER": colors[3],
                    "STOWEINN": colors[5]
                },
                'junior': {
                    "BRUNSWICKAPTS": colors[5],
                    "COLESTOWER": colors[3],
                    "STOWEINN": colors[5]
                },
                'senior': {
                    "BRUNSWICKAPTS": colors[5],
                    "COLESTOWER": colors[4],
                    "STOWEINN": colors[5]
                }
            },
            'double': {
                'sophomore': {
                    "BRUNSWICKAPTS": colors[5],
                    "COLESTOWER": colors[1],
                    "STOWEINN": colors[5],
                },
                'junior': {
                    "BRUNSWICKAPTS": colors[5],
                    "COLESTOWER": colors[1],
                    "STOWEINN": colors[5],
                },
                'senior': {
                    "BRUNSWICKAPTS": colors[5],
                    "COLESTOWER": colors[3],
                    "STOWEINN": colors[5],
                }
            },
            'single': {
                'sophomore': {
                    "STOWEINN": colors[1],
                    "CHAMBERLAIN": colors[1]
                },
                'junior': {
                    "STOWEINN": colors[2],
                    "CHAMBERLAIN": colors[1]
                },
                'senior': {
                    "STOWEINN": colors[5],
                    "CHAMBERLAIN": colors[4]
                }
            }
        }
    }
$(document).ready(function() {
    $('#class').change(function() { 
        classyear = $(this).find('option:selected')[0].value; 
        updateImage();
    });
    $('#block').change(function() { 
        blocksize = $(this).find('option:selected')[0].value;
        updateImage();
    });
    $('#chemfree').change(function() { 
        chemfree = $(this).find('option:selected')[0].value; 
        updateImage();
    });
    function updateImage() {
        console.log(classyear, blocksize, chemfree)
        if (classyear != null && blocksize != null && chemfree != null) {
            var currentdata = colorData[chemfree][blocksize][classyear];
            console.log(currentdata);
            $('.cls-9').velocity({
                fill: "#DFDFDF"
            });
            $('#HIDING_BOX').velocity({
                opacity: 0
            });
            $('.cls-7, .cls-8, .cls-3, .cls-5, .cls-6').velocity({
                fill: "#CCC",
                stroke: "999"
            });
            for (var key in currentdata) {
                var value = currentdata[key];
                $('#' + key + ' .cls-9').velocity({
                    fill: value
                });
            }
        }
    }
    /* PINE */
    $('#PINE_HOVER path').hover(function() {
        $('#PINEAPTS .cls-9').velocity({
            opacity: 0.5
        });
        $('.pine-info').show();
        $('.place-info').fadeIn(100);
    }, function() {
        $('#PINEAPTS .cls-9').velocity({
            opacity: 1
        });
        $('.place-info').fadeOut(100, function() {
            $('.pine-info').hide();
        });
    });
    /* 52 HARPS */
    $('#_52_HARPSWELL_HOVER path').hover(function() {
        $('#_52HARPSWELL .cls-9').velocity({
            opacity: 0.5
        });
        $('._52-harps-info').show();
        $('.place-info').fadeIn(100);
    }, function() {
        $('#_52HARPSWELL .cls-9').velocity({
            opacity: 1
        });
        $('.place-info').fadeOut(100, function() {
            $('._52-harps-info').hide();
        });
    });
    /* HOWARD */
    $('#HOWARD_HOVER path').hover(function() {
        $('#HOWARDHALL .cls-9').velocity({
            opacity: 0.5
        });
        $('.howard-info').show();
        $('.place-info').fadeIn(100);
    }, function() {
        $('#HOWARDHALL .cls-9').velocity({
            opacity: 1
        });
        $('.place-info').fadeOut(100, function() {
            $('.howard-info').hide();
        });
    });
    $('#STOWE_HOVER path').hover(function() {
        $('#STOWEHALL .cls-9').velocity({
            opacity: 0.5
        });
        $('.stowe-info').show();
        $('.place-info').fadeIn(100);
    }, function() {
        $('#STOWEHALL .cls-9').velocity({
            opacity: 1
        });
        $('.place-info').fadeOut(100, function() {
            $('.stowe-info').hide();
        });
    });
    $('#COLES_HOVER path').hover(function() {
        $('#COLESTOWER .cls-9').velocity({
            opacity: 0.5
        });
        $('.coles-info').show();
        $('.place-info').fadeIn(100);
    }, function() {
        $('#COLESTOWER .cls-9').velocity({
            opacity: 1
        });
        $('.place-info').fadeOut(100, function() {
            $('.coles-info').hide();
        });
    });
    $('#CHAMBO_HOVER path').hover(function() {
        $('#CHAMBERLAIN .cls-9').velocity({
            opacity: 0.5
        });
        $('.chambo-info').show();
        $('.place-info').fadeIn(100);
    }, function() {
        $('#CHAMBERLAIN .cls-9').velocity({
            opacity: 1
        });
        $('.place-info').fadeOut(100, function() {
            $('.chambo-info').hide();
        });
    });
    $('#BRUNSWICK_HOVER path').hover(function() {
        $('#BRUNSWICKAPTS .cls-9').velocity({
            opacity: 0.5
        });
        $('.brunswick-info').show();
        $('.place-info').fadeIn(100);
    }, function() {
        $('#BRUNSWICKAPTS .cls-9').velocity({
            opacity: 1
        });
        $('.place-info').fadeOut(100, function() {
            $('.brunswick-info').hide();
        });
    });
    $('#MAYFLOWER_HOVER path').hover(function() {
        $('#MAYFLOWERAPTS .cls-9').velocity({
            opacity: 0.5
        });
        $('.mayflower-info').show();
        $('.place-info').fadeIn(100);
    }, function() {
        $('#MAYFLOWERAPTS .cls-9').velocity({
            opacity: 1
        });
        $('.place-info').fadeOut(100, function() {
            $('.mayflower-info').hide();
        });
    });
    $('#STOWE_INN_HOVER path').hover(function() {
        $('#STOWEINN .cls-9').velocity({
            opacity: 0.5
        });
        $('.stowe-inn-info').show();
        $('.place-info').fadeIn(100);
    }, function() {
        $('#STOWEINN .cls-9').velocity({
            opacity: 1
        });
        $('.place-info').fadeOut(100, function() {
            $('.stowe-inn-info').hide();
        });
    });
    $('#HARPSWELL_HOVER path').hover(function() {
        $('#HARPSWELLAPTS .cls-9').velocity({
            opacity: 0.5
        });
        $('.harpswell-info').show();
        $('.place-info').fadeIn(100);
    }, function() {
        $('#HARPSWELLAPTS .cls-9').velocity({
            opacity: 1
        });
        $('.place-info').fadeOut(100, function() {
            $('.harpswell-info').hide();
        });
    });
    $('#SMITH_HOVER path').hover(function() {
        $('#SMITHHOUSE .cls-9').velocity({
            opacity: 0.5
        });
        $('.smith-info').show();
        $('.place-info').fadeIn(100);
    }, function() {
        $('#SMITHHOUSE .cls-9').velocity({
            opacity: 5
        });
        $('.place-info').fadeOut(100, function() {
            $('.smith-info').hide();
        });
    });
});
$(window).scroll(function() {
    var scroll = $(window).scrollTop();
    if (scroll >= 200) {
        $(".map").addClass("map-in-place");
    } else {
        $(".map").removeClass("map-in-place");
    }
});