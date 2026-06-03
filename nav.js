// nav.js - toggles mobile navigation menu
document.addEventListener('DOMContentLoaded', function () {
  const burger = document.getElementById('burger');
  const navLinks = document.getElementById('nav-links');
  if (burger && navLinks) {
    burger.addEventListener('click', function () {
      navLinks.classList.toggle('active');
      // animate burger
      burger.classList.toggle('open');
    });
  }
});
