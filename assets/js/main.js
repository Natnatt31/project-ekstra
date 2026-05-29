// ==========================================
// Globaliti Esport Badung — Main JavaScript
// ==========================================

document.addEventListener('DOMContentLoaded', function () {

  // --- Loading Screen ---
  const loader = document.querySelector('.loader');
  if (loader) {
    window.addEventListener('load', () => {
      setTimeout(() => loader.classList.add('hidden'), 800);
    });
    // Fallback
    setTimeout(() => loader.classList.add('hidden'), 3000);
  }

  // --- Navbar Scroll Effect ---
  const navbar = document.querySelector('.navbar');
  const backToTop = document.querySelector('.back-to-top');

  window.addEventListener('scroll', () => {
    const scrollY = window.scrollY;

    // Navbar background
    if (scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }

    // Back to top button
    if (backToTop) {
      if (scrollY > 500) {
        backToTop.classList.add('visible');
      } else {
        backToTop.classList.remove('visible');
      }
    }

    // Active nav link based on scroll position
    updateActiveNavLink();
  });

  // --- Back to Top ---
  if (backToTop) {
    backToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // --- Mobile Menu ---
  const hamburger = document.querySelector('.hamburger');
  const mobileMenu = document.querySelector('.mobile-menu');
  const mobileOverlay = document.querySelector('.mobile-overlay');
  const mobileLinks = document.querySelectorAll('.mobile-menu .nav-link');

  function toggleMobileMenu() {
    hamburger.classList.toggle('active');
    mobileMenu.classList.toggle('active');
    mobileOverlay.classList.toggle('active');
    document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
  }

  if (hamburger) {
    hamburger.addEventListener('click', toggleMobileMenu);
  }
  if (mobileOverlay) {
    mobileOverlay.addEventListener('click', toggleMobileMenu);
  }
  mobileLinks.forEach(link => {
    link.addEventListener('click', () => {
      if (mobileMenu.classList.contains('active')) toggleMobileMenu();
    });
  });

  const mobileClose = document.querySelector('#mobileClose');
  if (mobileClose) {
    mobileClose.addEventListener('click', toggleMobileMenu);
  }

  // --- Active Nav Link on Scroll ---
  function updateActiveNavLink() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');

    let current = '';
    sections.forEach(section => {
      const sectionTop = section.offsetTop - 120;
      if (window.scrollY >= sectionTop) {
        current = section.getAttribute('id');
      }
    });

    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href') === '#' + current) {
        link.classList.add('active');
      }
    });
  }

  // --- Typewriter Effect ---
  const typewriterEl = document.querySelector('.typewriter-text');
  if (typewriterEl) {
    const texts = [
      'Globaliti Esport Badung',
      'Dominasi Arena Digital',
      'Bersatu, Bertanding, Berjaya'
    ];
    let textIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    let typingSpeed = 100;

    function typeWriter() {
      const currentText = texts[textIndex];

      if (isDeleting) {
        typewriterEl.textContent = currentText.substring(0, charIndex - 1);
        charIndex--;
        typingSpeed = 50;
      } else {
        typewriterEl.textContent = currentText.substring(0, charIndex + 1);
        charIndex++;
        typingSpeed = 100;
      }

      if (!isDeleting && charIndex === currentText.length) {
        typingSpeed = 2000;
        isDeleting = true;
      } else if (isDeleting && charIndex === 0) {
        isDeleting = false;
        textIndex = (textIndex + 1) % texts.length;
        typingSpeed = 500;
      }

      setTimeout(typeWriter, typingSpeed);
    }

    setTimeout(typeWriter, 1000);
  }

  // --- Counter Animation ---
  const counters = document.querySelectorAll('.stat-number[data-target]');
  let countersAnimated = false;

  function animateCounters() {
    counters.forEach(counter => {
      const target = parseInt(counter.getAttribute('data-target'));
      const suffix = counter.getAttribute('data-suffix') || '';
      const duration = 2000;
      const step = target / (duration / 16);
      let current = 0;

      function updateCounter() {
        current += step;
        if (current < target) {
          counter.textContent = Math.floor(current) + suffix;
          requestAnimationFrame(updateCounter);
        } else {
          counter.textContent = target + suffix;
        }
      }

      updateCounter();
    });
    countersAnimated = true;
  }

  // Observe counter section
  const aboutSection = document.querySelector('#about');
  if (aboutSection && counters.length > 0) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !countersAnimated) {
          animateCounters();
        }
      });
    }, { threshold: 0.3 });
    observer.observe(aboutSection);
  }

  // --- Roster Tab Filtering ---
  const rosterTabs = document.querySelectorAll('.roster-tab');
  const rosterPanels = document.querySelectorAll('.roster-panel');

  rosterTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const game = tab.getAttribute('data-game');

      // Update active tab
      rosterTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      // Show/hide panels
      rosterPanels.forEach(panel => {
        if (panel.getAttribute('data-game') === game) {
          panel.style.display = 'block';
          // Re-init swiper for this panel
          const swiperEl = panel.querySelector('.swiper');
          if (swiperEl && swiperEl.swiper) {
            swiperEl.swiper.update();
          }
        } else {
          panel.style.display = 'none';
        }
      });
    });
  });

  // --- AOS Init ---
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 800,
      easing: 'ease-out-cubic',
      once: true,
      offset: 20,
      delay: 0
    });
    // Refresh AOS after images load
    window.addEventListener('load', () => {
      AOS.refresh();
    });
  }

  // --- GLightbox Init ---
  if (typeof GLightbox !== 'undefined') {
    GLightbox({
      selector: '.glightbox',
      touchNavigation: true,
      loop: true,
      autoplayVideos: true
    });
  }

  // --- Smooth scroll for CTA buttons ---
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });

});
