function UItoTop(options) {
    var defaults = {
        text: 'To Top',
        min: 200,
        inDelay: 600,
        outDelay: 400,
        containerID: 'toTop',
        containerHoverID: 'toTopHover',
        scrollSpeed: 1000,
        easingType: 'linear'
    };

    var settings = Object.assign({}, defaults, options);

    // Create the "To Top" button and append it to the body
    var toTopButton = document.createElement('a');
    toTopButton.href = '#';
    toTopButton.id = settings.containerID;
    toTopButton.textContent = settings.text;
    toTopButton.style.display = 'none';
    document.body.appendChild(toTopButton);

    // Create the hover span element
    var hoverSpan = document.createElement('span');
    hoverSpan.id = settings.containerHoverID;
    toTopButton.prepend(hoverSpan);

    // Handle the click event to scroll to the top
    toTopButton.addEventListener('click', function(event) {
        event.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });

        hoverSpan.style.opacity = '0';
    });

    // Handle hover in and out events
    toTopButton.addEventListener('mouseenter', function() {
        hoverSpan.style.transition = 'opacity 0.6s linear';
        hoverSpan.style.opacity = '1';
    });

    toTopButton.addEventListener('mouseleave', function() {
        hoverSpan.style.transition = 'opacity 0.7s linear';
        hoverSpan.style.opacity = '0';
    });

    // Show or hide the button on scroll
    window.addEventListener('scroll', function() {
        var scrollDistance = window.scrollY;
        if (typeof document.body.style.maxHeight === 'undefined') {
            toTopButton.style.position = 'absolute';
            toTopButton.style.top = (scrollDistance + window.innerHeight - 50) + 'px';
        }
        if (scrollDistance > settings.min) {
            toTopButton.style.transition = `opacity ${settings.inDelay}ms`;
            toTopButton.style.display = 'block';
            toTopButton.style.opacity = '1';
        } else {
            toTopButton.style.transition = `opacity ${settings.outDelay}ms`;
            toTopButton.style.opacity = '0';
            setTimeout(function() {
                toTopButton.style.display = 'none';
            }, settings.outDelay);
        }
    });
}

UItoTop({
    easingType: 'easeOutQuart'
})
