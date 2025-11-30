// Mobile Menu Functionality
const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const mobileMenu = document.getElementById('mobileMenu');
const closeMenu = document.getElementById('closeMenu');
const overlay = document.getElementById('overlay');
const hamburgerIcon = document.querySelector('.hamburger-icon');
const mobileServicesToggle = document.getElementById('mobileServicesToggle');
const mobileServicesDropdown = document.getElementById('mobileServicesDropdown');

// Toggle mobile menu
function toggleMobileMenu() {
    mobileMenu.classList.toggle('active');
    overlay.classList.toggle('active');
    hamburgerIcon.classList.toggle('active');
    document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
}

// Event listeners
mobileMenuToggle.addEventListener('click', toggleMobileMenu);
closeMenu.addEventListener('click', toggleMobileMenu);
overlay.addEventListener('click', toggleMobileMenu);

// Toggle services dropdown in mobile menu
mobileServicesToggle.addEventListener('click', function(e) {
    e.preventDefault();
    mobileServicesDropdown.classList.toggle('active');
    mobileServicesToggle.classList.toggle('active');
});

// Close mobile menu when clicking on a link (except dropdown toggle)
document.querySelectorAll('.mobile-nav-link:not(.dropdown-toggle)').forEach(link => {
    link.addEventListener('click', toggleMobileMenu);
});

// Close mobile menu on window resize if it's open and window is larger than 992px
window.addEventListener('resize', function() {
    if (window.innerWidth > 992 && mobileMenu.classList.contains('active')) {
        toggleMobileMenu();
    }
});

  // Reveal service cards on scroll
  document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll(".service-card");

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("visible");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15 }
    );

    cards.forEach((card) => observer.observe(card));
  });

 // this is experience section
  // Counter animation using IntersectionObserver
        document.addEventListener("DOMContentLoaded", function () {
            const counters = document.querySelectorAll(".experience-number");
            let hasAnimated = false; // taaki bar-bar animate na ho

            function animateCounters() {
                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute("data-target"), 10);
                    const suffix = counter.getAttribute("data-suffix") || "";
                    let current = 0;
                    const duration = 1500; // ms
                    const startTime = performance.now();

                    function update(now) {
                        const elapsed = now - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        current = Math.floor(progress * target);
                        counter.textContent = current + suffix;

                        if (progress < 1) {
                            requestAnimationFrame(update);
                        } else {
                            counter.textContent = target + suffix; // ensure exact
                        }
                    }

                    requestAnimationFrame(update);
                });
            }

            const section = document.querySelector(".experience-section");

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !hasAnimated) {
                        hasAnimated = true;
                        animateCounters();
                    }
                });
            }, { threshold: 0.3 });

            observer.observe(section);
        });

        