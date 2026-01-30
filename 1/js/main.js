const swiper = document.querySelector('.swiper');

//скрипты для главной страницы
if (swiper) {
  function scrollUp() {
    const scrollUp = document.querySelector('.scrollUp');
    const firstLink = document.querySelector('.header__a.one');

    window.addEventListener('scroll', () => {
      firstLink.classList.remove('active');

      if (scrollY > 499) {
        scrollUp.classList.add('active');
      } else {
        scrollUp.classList.remove('active');
      }
    });
  }

  function swiperFunction() {
    const swiper = new Swiper('.swiper-revievs', {
      speed: 400,
      spaceBetween: 30,
      slidesPerView: 1,
      autoHeight: true,

      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    });
  }

  function animationMonetization() {
    window.addEventListener('scroll', () => {
      const monetization = document.querySelector('.monetization__items');
      const animItemHeight = monetization.offsetHeight;
      const animItemOffset = offset(monetization).top;
      const animStart = 4;
      let animItemPoint = window.innerHeight - animItemHeight / animStart;

      if (scrollY > animItemOffset - animItemPoint && scrollY < animItemOffset + animItemHeight) {
        monetization.classList.add('active');
      }
    });
  }

  function animationQuestions() {
    window.addEventListener('scroll', () => {
      const questions = document.querySelector('.questions__items');
      const imagesBlock = document.querySelector('.questions__images');
      const animItemHeight = questions.offsetHeight;
      const animItemOffset = offset(questions).top;
      const animStart = 4;
      let animItemPoint = window.innerHeight - animItemHeight / animStart;

      if (scrollY > animItemOffset - animItemPoint && scrollY < animItemOffset + animItemHeight) {
        imagesBlock.classList.add('active');
      }
    });
  }

  function offset(el) {
    const rect = el.getBoundingClientRect(),
      scrollLeft = window.pageXOffset || document.documentElement.scrollLeft,
      scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    return { top: rect.top + scrollTop, left: rect.left + scrollLeft };
  }

  function anchors() {
    //выделение пункта при скроле
    var sections = $('section'),
      nav = $('nav'),
      nav_height = nav.outerHeight();

    $(window).on('scroll', function () {
      var cur_pos = $(this).scrollTop();

      sections.each(function () {
        var top = $(this).offset().top - nav_height - 95,
          bottom = top + $(this).outerHeight() - 95;

        if (cur_pos >= top && cur_pos <= bottom) {
          nav.find('a').removeClass('active');
          sections.removeClass('active');

          $(this).addClass('active');
          nav.find('a[href="#' + $(this).attr('id') + '"]').addClass('active');
        }
      });
    });
  }

  function accardion() {
    const item = document.querySelector('.questions__items');
    const items = document.querySelectorAll('.questions__item-title');

    item.addEventListener('click', (e) => {
      if (e.target.closest('.questions__item-title')) {
        let currentItem = e.target.closest('.questions__item-title');
        currentItem.classList.toggle('active');

        items.forEach((item) => {
          if (currentItem === item) return false;
          item.classList.remove('active');
        });
      }
    });
  }

  function checkStorage() {
    document.addEventListener('DOMContentLoaded', () => {
      const widthScreen = window.innerWidth;
      const ratesSection = document.getElementById('rates');
      const rates = localStorage.getItem('rates');
      const partner = localStorage.getItem('partner');
      const questions = localStorage.getItem('questions');

      if (widthScreen > 970) {
        if (ratesSection && rates) {
          $(document).ready(function () {
            $('html, body').animate(
              {
                scrollTop: $('#rates').offset().top - 115,
              },
              'slow',
            );
          });

          localStorage.clear();
        }
      } else {
        if (ratesSection && rates) {
          $(document).ready(function () {
            $('html, body').animate(
              {
                scrollTop: $('#rates').offset().top - 70,
              },
              'slow',
            );
          });

          localStorage.clear();
        }
      }

      if (ratesSection && partner) {
        $(document).ready(function () {
          $('html, body').animate(
            {
              scrollTop: $('#partner').offset().top - 80,
            },
            'slow',
          );
        });

        localStorage.clear();
      }

      if (ratesSection && questions) {
        $(document).ready(function () {
          $('html, body').animate(
            {
              scrollTop: $('#questions').offset().top - 70,
            },
            'slow',
          );
        });

        localStorage.clear();
      }
    });
  }

  function toggleRates() {
    const btn = document.querySelector('.rates__toggle');
    const items = document.querySelectorAll('.rates__item');

    btn.addEventListener('click', () => {
      btn.classList.toggle('active');
      items.forEach((item) => {
        item.classList.toggle('active');
      });
    });
  }

  scrollUp();
  swiperFunction();
  animationMonetization();
  animationQuestions();
  anchors();
  accardion();
  checkStorage();
  toggleRates();
}

function showSwiper() {
  let widthScreen = window.innerWidth;

  if (widthScreen < 1161) {
    let rates = new Swiper('.swiper-hidden', {
      freeMode: true,
      slidesPerView: 'auto',
      spaceBetween: 20,
    });
  }
}

showSwiper();

//скрипт на страницах rates
function backToRates() {
  const arrowBack = document.querySelector('.rate__link');
  const linkRatesPage = document.querySelector('.back-to-home');

  arrowBack?.addEventListener('click', () => {
    localStorage.setItem('rates', 'true');
  });

  linkRatesPage?.addEventListener('click', () => {
    localStorage.setItem('rates', 'true');
  });
}

function navBackToRates() {
  const backRates = document.querySelector('.header__a.two');
  const backPartner = document.querySelector('.header__a.three');
  const backQuestion = document.querySelector('.header__a.four');
  const backRatesMobile = document.querySelector('.mobile-menu__link.two');
  const backPartnerMobile = document.querySelector('.mobile-menu__link.three');
  const backQuestionMobile = document.querySelector('.mobile-menu__link.four');

  backRates?.addEventListener('click', () => {
    localStorage.setItem('rates', 'true');
  });

  backPartner?.addEventListener('click', () => {
    localStorage.setItem('partner', 'true');
  });

  backQuestion?.addEventListener('click', () => {
    localStorage.setItem('questions', 'true');
  });

  backRatesMobile?.addEventListener('click', () => {
    localStorage.setItem('rates', 'true');
  });

  backPartnerMobile?.addEventListener('click', () => {
    localStorage.setItem('partner', 'true');
  });

  backQuestionMobile?.addEventListener('click', () => {
    localStorage.setItem('questions', 'true');
  });
}

function showMore() {
  const btn = document.querySelector('.rate__more');
  const hiddenContent = document.querySelector('.rate__hidden-content');

  btn?.addEventListener('click', () => {
    hiddenContent.classList.toggle('active');
  });
}

backToRates();
navBackToRates();
showMore();

//скрипты на всех страницах
function menuAnimation() {
  const menu = document.querySelector('.header__ul');

  menu.addEventListener('mouseover', (event) => {
    if (event.target.classList.contains('header__a')) {
      menu.style.setProperty('--underline-width', `${event.target.offsetWidth}px`);
      menu.style.setProperty('--underline-offset-x', `${event.target.offsetLeft}px`);
    }
  });
  menu.addEventListener('mouseleave', () => menu.style.setProperty('--underline-width', '0'));
}

function mobileMenu() {
  const burger = document.querySelector('.burger');
  const links = document.querySelectorAll('.mobile-menu__link');
  const body = document.body;

  burger.addEventListener('click', () => {
    body.classList.toggle('active');
    burger.classList.toggle('active');
  });

  body.addEventListener('click', () => {
    body.classList.remove('active');
    burger.classList.remove('active');
  });

  links.forEach((link) => {
    link.addEventListener('click', () => {
      body.classList.remove('active');
      burger.classList.remove('active');
    });
  });
}

function anchor() {
  //плавная анимация якорей
  const widthScreen = window.innerWidth;
  $("a[href='#questions']").on('click', function () {
    let href = $(this).attr('href');

    $('html, body').animate(
      {
        scrollTop: $(href).offset().top - 60,
      },
      'slow',
    );
  });

  $("a[href='#partner']").on('click', function () {
    let href = $(this).attr('href');

    $('html, body').animate(
      {
        scrollTop: $(href).offset().top - 75,
      },
      'slow',
    );
  });

  if (widthScreen > 970) {
    $("a[href='#rates']").on('click', function () {
      let href = $(this).attr('href');

      $('html, body').animate(
        {
          scrollTop: $(href).offset().top - 100,
        },
        'slow',
      );
    });
  } else {
    $("a[href='#rates']").on('click', function () {
      let href = $(this).attr('href');

      $('html, body').animate(
        {
          scrollTop: $(href).offset().top - 70,
        },
        'slow',
      );
    });
  }

  $("a[href='#service']").on('click', function () {
    let href = $(this).attr('href');

    $('html, body').animate(
      {
        scrollTop: $(href).offset().top - 95,
      },
      'slow',
    );
  });
}

function formValidation() {
  //находим все формы на сайте с классом 'form' и валидируем их в зависимости от класса инпута
  const forms = document.querySelectorAll('.form');

  forms.forEach((form) => {
    isValidate(form);
  });

  function isValidate(form) {
    const validation = new JustValidate(form);
    const telSelector = form.querySelector('.login__phone');

    if (telSelector) {
      const inputMask = new Inputmask('+7 (999) 999-99-99');
      inputMask.mask(telSelector);
    }

    if (form.querySelector('.login__name')) {
      validation.addField('.login__name', [
        {
          rule: 'minLength',
          value: 2,
        },
        {
          rule: 'required',
          value: true,
        },
      ]);
    }

    if (form.querySelector('.login__phone')) {
      validation.addField('.login__phone', [
        {
          rule: 'required',
          value: true,
        },
        {
          rule: 'function',
          validator: function () {
            let phone = telSelector.inputmask.unmaskedvalue();
            return phone.length === 10;
          },
          errorMessage: 'Введите корректный  телефон',
        },
      ]);
    }

    if (form.querySelector('.login__email')) {
      validation.addField('.login__email', [
        {
          rule: 'required',
          value: true,
        },
        {
          rule: 'email',
          value: true,
        },
      ]);
    }

    if (form.querySelector('.login__password')) {
      validation.addField('.login__password', [
        {
          rule: 'required',
          value: true,
        },
        {
          rule: 'password',
          value: true,
        },
      ]);
    }

    if (form.querySelector('.input__checkbox-1')) {
      validation.addField('.input__checkbox-1', [
        {
          rule: 'required',
        },
      ]);
    }

    if (form.querySelector('.input__checkbox-2')) {
      validation.addField('.input__checkbox-2', [
        {
          rule: 'required',
        },
      ]);
    }

    //если все обязательные поля корректно заполнены, то отправляем форму
    validation.onSuccess((event) => {
      event.preventDefault();
      const formData = new FormData(event.target);
      console.log(...formData);

      fetch('mail.php', {
        method: 'POST',
        body: formData,
      })
        .then((response) => {
          if (response.ok) {
            event.target.reset();
          } else {
            throw new Error('Form submission failed.');
          }
        })
        .catch((error) => {
          console.error(error);
          alert('An error occurred while submitting the form.');
        });
    });
  }
}

menuAnimation();
mobileMenu();
anchor();
formValidation();
