<div class="header-mobile header_sticky">
    <div class="container d-flex align-items-center h-100">
      <a class="mobile-nav-activator d-block position-relative" href="#">
        <svg class="nav-icon" width="25" height="18" viewBox="0 0 25 18" xmlns="http://www.w3.org/2000/svg">
          <use href="#icon_nav" />
        </svg>
        <button class="btn-close-lg position-absolute top-0 start-0 w-100"></button>
      </a>

      <div class="logo">
        <a href="{{route('shop')}}" class="fs-3">
          Pondok Mawar
        </a>
      </div>

      <a href="{{route('cart')}}" class="header-tools__item header-tools__cart" data-aside="">
        <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <use href="#icon_cart" />
        </svg>
        <span class="cart-amount d-block position-absolute js-cart-items-count">{{ $cartItemCount }}</span>
      </a>
    </div>

    <nav
      class="header-mobile__navigation navigation d-flex flex-column w-100 position-absolute top-100 bg-body overflow-auto">
      <div class="container">
        <form action="{{ route('shop.search') }}" method="GET" class="search-field position-relative mt-4 mb-3">

          <div class="position-relative">
            <input id="search-input-mobile" class="search-field__input w-100 border rounded-1" type="text" name="q"
       placeholder="Search products" autocomplete="off" />
            <button class="btn-icon search-popup__submit pb-0 me-2" type="submit">
              <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_search" />
              </svg>
            </button>
            <button class="btn-icon btn-close-lg search-popup__reset pb-0 me-2" type="reset"></button>
          </div>

          <div class="position-absolute start-0 top-100 m-0 w-100">
            <div class="search-result">
              <div class="suggestion-box suggestion-box-mobile bg-white shadow border rounded mt-1 d-none position-absolute w-100"></div>
            </div>
          </div>
        </form>
      </div>

      <div class="container">
        <div class="overflow-hidden">
          <ul class="navigation__list list-unstyled position-relative">
            <li class="navigation__item">
              <a href="{{route('shop')}}" class="navigation__link">Home</a>
            </li>
            <li class="navigation__item">
              <a href="{{route('shop')}}" class="navigation__link">Shop</a>
            </li>
            <li class="navigation__item">
              <a href="{{route('cart')}}" class="navigation__link">Cart</a>
            </li>
            <li class="navigation__item">
              <a href="about.html" class="navigation__link">About</a>
            </li>
            <li class="navigation__item">
              <a href="contact.html" class="navigation__link">Contact</a>
            </li>
          </ul>
        </div>
      </div>

      <div class="border-top mt-auto pb-2">
        

        @guest
          <div class="customer-links container mt-4 mb-2 pb-1">
            <svg class="d-inline-block align-middle" width="20" height="20" viewBox="0 0 20 20" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <use href="#icon_user" />
            </svg>
            <!-- <span >My Account</span> -->
            <a href="{{route('pages.account')}}" class="d-inline-block ms-2 text-uppercase align-middle fw-medium">My Account</a>
          </div>
          @else             
            <div class="customer-links container mt-4 mb-2 pb-1">
              <svg class="d-inline-block align-middle" width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_user" />
              </svg>
              <!-- <span >My Account</span> -->
              <a href="{{ Auth::user()->role=='admin' ? route('admin.index') : route('pages.account')}}" class="d-inline-block ms-2 text-uppercase align-middle fw-medium">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</a>
            </div>       
          @endif
      </div>
    </nav>
  </div>


  <header id="header" class="header header-fullwidth header-transparent-bg">
    <div class="container">
      <div class="header-desk header-desk_type_1">
        <div class="logo">
          <a href="{{route('shop')}}" class="fs-4">
            Pondok Mawar
          </a>
        </div>

        <div class="header-tools d-flex align-items-center">
          <div class="header-tools__item hover-container">
            <div class="position-relative" style="max-width: 500px;">
              <form action="{{ route('shop.search') }}" method="GET" class="search-field container">
                <input id="search-input-desktop"
                      class="search-field__input search-popup__input fw-medium pe-5"
                      style="width: 500px;"
                      type="text"
                      name="q"
                      placeholder="Search products"
                      autocomplete="off" />

                <!-- Ikon search hanya visual, bukan button -->
                <svg class="position-absolute top-50 end-0 translate-middle-y me-2"
                    width="16" height="16" fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                    style="pointer-events: none; opacity: 0.6;">
                  <use href="#icon_search" />
                </svg>
              </form>
            </div>
          </div>

          @guest
          <div class="header-tools__item hover-container">
              <a class="header-tools__item" href="{{route('login')}}">
                <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_user" />
                </svg>
              </a>
            </div>
          @else                    
            <div class="header-tools__item hover-container">
              <a class="header-tools__item" href="{{ Auth::user()->role=='admin' ? route('admin.index') : route('pages.account')}}">
                <span class="pr-6px">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</span>
                <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_user" />
                </svg>
              </a>
            </div>
          @endif     

          <a href="{{route('cart')}}" class="header-tools__item header-tools__cart">
            <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <use href="#icon_cart" />
            </svg>
            <span class="cart-amount d-block position-absolute js-cart-items-count">{{ $cartItemCount }}</span>
          </a>
        </div>
      </div>
    </div>
  </header>