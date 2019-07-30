@extends('layouts.main')

@section('content')
<div id="app">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="#/">
                    <!--<img src="images/Logo.png" \>-->
                    Distribuidora
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            Repartos <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="#/allocations">Entregas</a></li>
                            <li><a href="#/liquidations">Liquidaciones</a></li>
                            <li><a href="#/devolutions">Devoluciones</a></li>
                        </ul>
                    </li>
                    <li><a href="#/expenses">Gastos</a></li>
                    <li><a href="#/movements">Movimientos</a></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            Catálogos <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="#/salespersons">Vendedores</a></li>
                            <li><a href="#/brands">Marcas</a></li>
                            <li><a href="#/warehouses">Almacenes</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#/users">Usuarios</a></li>
                        </ul>
                    </li>
                    <li><a href="#/reports">Reportes</a></li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="#/configuration">Configuración</a>
                                    <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid cls-main-container">
      <div data-ng-view></div>
    </div>
</div>
@endsection
