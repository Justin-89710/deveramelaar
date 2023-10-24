document.addEventListener('DOMContentLoaded', function () {
    var firstButton = document.querySelector('.first-button');
    var animatedIcon1 = document.querySelector('.animated-icon1');

    var secondButton = document.querySelector('.second-button');
    var animatedIcon2 = document.querySelector('.animated-icon2');

    var thirdButton = document.querySelector('.third-button');
    var animatedIcon3 = document.querySelector('.animated-icon3');

    firstButton.addEventListener('click', function () {
        animatedIcon1.classList.toggle('open');
    });

    secondButton.addEventListener('click', function () {
        animatedIcon2.classList.toggle('open');
    });

    thirdButton.addEventListener('click', function () {
        animatedIcon3.classList.toggle('open');
    });
});