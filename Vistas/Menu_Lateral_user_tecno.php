<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div id="navbar-menu">
            <?php
            if (isset($logoSecundario_blob) && !empty($logoSecundario_blob)) {
                $base64Image = base64_encode($logoSecundario_blob);
            } else {
                // Si $logoPrincipal_blob no está definido o está vacío, usa una URL por defecto
                $base64Image = base64_encode(file_get_contents('../img/Logos Municipios/Fondo_Defecto.png'));
            } ?>
            <img src="data:image/jpeg;base64,<?php echo $base64Image; ?>" alt="Defecto" class="Fondo_Defecto">
            <ul class="nav navbar-nav navbar-right">

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="lnr lnr-question-circle"></i>
                        <i class="fa-solid fa-gear"></i>
                        <span>Configuración</span>
                        <i class="icon-submenu lnr lnr-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#perfil">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Perfil
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#Cerrar_sesion">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Cerrar Sesión
                            </a>
                        </li>

                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="sidebar-nav" class="sidebar">
    <div class="sidebar-scroll">
        <nav>
            <div class="sidebar">
                <div class="sidebar-content">
                    <div class="fondo_user_menu">

                        <svg xmlns="http://www.w3.org/2000/svg" class="igm_logo_empresarial" viewBox="0 0 532 532" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin-top: -21px;">
                            <circle cx="270.75986" cy="260.93427" r="86.34897" fill="#ffb6b6" />
                            <polygon points="221.18982 360.05209 217.28876 320.6185 295.18982 306.05209 341.18982 418.05209 261.18982 510.05209 204.18982 398.05209 221.18982 360.05209" fill="#ffb6b6" />
                            <path d="m216.0374,340.35736l17.03111,3.84802s-13.38821-42.45453-8.84396-46.50766c4.54427-4.05316,15.68007,2.33328,15.68007,2.33328l11.70201,13.1199,14.25394-14.51239s15.47495-19.2421,21.53397-24.6463-3.67319-25.46364-3.67319-25.46364c0,0,89.89185-24.23923,56.44299-67.83968,0,0-19.61093-34.18452-25.99734-23.04871-6.38641,11.1358-14.00162-6.55013-14.00162-6.55013l-23.25381,4.42198s-45.89429-27.06042-89.45331,30.82959c-43.55902,57.89003,28.57916,154.01572,28.57916,154.01572h-.00002Z" fill="#2f2e41" />
                            <path d="m433.16003,472.95001c-47.19,38.26001-105.57001,59.04999-167.16003,59.04999-56.23999,0-109.81-17.33997-154.62-49.47998.08002-.84003.16003-1.66998.23004-2.5,1.19-13,2.25-25.64001,2.94995-36.12,2.71002-40.69,97.64001-67.81,97.64001-67.81,0,0,.42999.42999,1.29004,1.17999,5.23999,4.59998,26.50995,21.27997,63.81,25.94,33.25995,4.15997,44.20996-15.57001,47.51996-25.02002,1-2.88,1.30005-4.81,1.30005-4.81l97.63995,46.10999c6.37,9.10004,8.86005,28.70001,9.35004,50.73004.01996.90997.03998,1.81.04999,2.72998Z" fill="var(--color-primario)" />
                            <path>
                        </svg>
                        <p><?php echo $nombre . " " . $apellidos ?> </p>
                    </div>
                    <ul class="lists">
                        <li class="list">
                            <a href="ptn_tecno_inicio.php" class="nav-link ">
                                <i class="bx bx-home-alt icon"></i>
                                <span class="link">Módulos</span>
                            </a>
                        </li>

                        <li class="list">
                            <a href="#" class="nav-link" data-toggle="modal" data-target="#Cerrar_sesion">
                                <i class="bx bx-log-out icon"></i>
                                <span class="link">Cerrar Sesión</span>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>


<!-- Modal -->
<div class="modal fade bs-example-modal-sm" id="perfil" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Mi perfil</h4>
            </div>
            <div class="modal-body">
                <div class="Conten_user">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icono_usuario" viewBox="0 0 532 532" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin-top: -21px;">
                        <circle cx="270.75986" cy="260.93427" r="86.34897" fill="#ffb6b6" />
                        <polygon points="221.18982 360.05209 217.28876 320.6185 295.18982 306.05209 341.18982 418.05209 261.18982 510.05209 204.18982 398.05209 221.18982 360.05209" fill="#ffb6b6" />
                        <path d="m216.0374,340.35736l17.03111,3.84802s-13.38821-42.45453-8.84396-46.50766c4.54427-4.05316,15.68007,2.33328,15.68007,2.33328l11.70201,13.1199,14.25394-14.51239s15.47495-19.2421,21.53397-24.6463-3.67319-25.46364-3.67319-25.46364c0,0,89.89185-24.23923,56.44299-67.83968,0,0-19.61093-34.18452-25.99734-23.04871-6.38641,11.1358-14.00162-6.55013-14.00162-6.55013l-23.25381,4.42198s-45.89429-27.06042-89.45331,30.82959c-43.55902,57.89003,28.57916,154.01572,28.57916,154.01572h-.00002Z" fill="#2f2e41" />
                        <path d="m433.16003,472.95001c-47.19,38.26001-105.57001,59.04999-167.16003,59.04999-56.23999,0-109.81-17.33997-154.62-49.47998.08002-.84003.16003-1.66998.23004-2.5,1.19-13,2.25-25.64001,2.94995-36.12,2.71002-40.69,97.64001-67.81,97.64001-67.81,0,0,.42999.42999,1.29004,1.17999,5.23999,4.59998,26.50995,21.27997,63.81,25.94,33.25995,4.15997,44.20996-15.57001,47.51996-25.02002,1-2.88,1.30005-4.81,1.30005-4.81l97.63995,46.10999c6.37,9.10004,8.86005,28.70001,9.35004,50.73004.01996.90997.03998,1.81.04999,2.72998Z" fill="var(--color-primario)" />
                        <path>
                    </svg>
                    <h4><?= $nombre; ?> <?= $apellidos; ?></h4><br>
                    <ul>
                        <li>
                            <p><i class="fa-solid fa-person"></i> Tipo de Usuario: <?= $Tipo_Usuario; ?></p>
                        </li>
                        <li>
                            <p><i class="fa-solid fa-user"></i> Usuario: <?= $Usuario; ?></p>
                        </li>
                        <li>
                            <p><i class="fa-solid fa-signal"></i> Estado: Activo🟢</p>
                        </li>
                        <li>
                            <p><i class="fa-solid fa-envelope-circle-check"></i> Mail: <?= $Mail; ?></p>
                        </li>
                    </ul>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>

            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="Cerrar_sesion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel">Cerrar sesión</h4>
            </div>
            <div class="modal-body">
                <center>¿Seguro que deseas cerrar sesión?</center>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a type="button" href="cerrar_sesion/cerrar_sesion.php" class="btn btn-primary">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>