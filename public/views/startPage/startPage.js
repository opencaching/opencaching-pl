$(document).ready(function(){
    $('.totalStatsSlider').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        dots: true,
        arrows: false,
        responsive: [{
            breakpoint: 970,
			settings: {
                slidesToShow: 4,
            }
        }, {
            breakpoint: 800,
			settings: {
                slidesToShow: 3,
            }
        }, {
            breakpoint: 500,
			settings: {
                slidesToShow: 2,
				slidesToScroll: 2,
            }
        }]
    });
});