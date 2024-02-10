<style>
    .Menu_secundario {
        position: fixed;
        display: flex;
        justify-content: space-around;
        align-items: center;
        z-index: 1;
        bottom: 0;
        left: 0;
        height: 50px;
        width: 100%;
        background-color: #3B3A40;
        padding: 10px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .Menu_secundario {
        display: none;
    }

    .Menu_secundario a {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: black;
        /* Ajusta el color del texto según tus preferencias */
    }

    .Menu_secundario .n {
        font-size: 20px;
        /* Ajusta el tamaño del icono según tus preferencias */
        margin-bottom: 5px;
        /* Ajusta el espaciado entre el icono y el texto según tus preferencias */
        transition: all 0.3s ease;
    }

    i .n {
        background-color: white;
    }

    .Menu_secundario_li {
        display: contents;
    }

    .Menu_secundario_ul {
        display: contents;
    }
</style>
<div class="Menu_secundario">
    <ul class="Menu_secundario_ul">
        <?php
        if ($Pagos == 1) {
        ?>
            <li class="Menu_secundario_li">
                <a href="ptn_pante_pagos.php" class="nav-link">
                    <i class="fa-regular fa-credit-card icon"></i>
                </a>
            </li>
        <?php
        } // Agrega esta llave de cierre para corregir la estructura
        ?>

        <?php
        if ($Titulares == 1) {
        ?>
            <li class="Menu_secundario_li">
                <a href="ptn_pante_titulares.php" class="nav-link ">
                    <i class="fa-regular fa-user icon"></i>
                </a>
            </li>
        <?php
        } // Agrega esta llave de cierre para corregir la estructura
        ?>

        <?php
        if ($Finados == 1) {
        ?>
            <li class="Menu_secundario_li">
                <a href="ptn_pante_finados.php" class="nav-link">
                    <i class="fa-solid fa-users icon"></i>
                </a>
            </li>
        <?php
        } // Agrega esta llave de cierre para corregir la estructura
        ?>

        <?php
        if ($Tumbas == 1) {
        ?>
            <li class="Menu_secundario_li">
                <a href="ptn_pante_tumbas.php" class="nav-link ">
                    <i class="fa-solid fa-cross icon"></i>
                </a>
            </li>
        <?php
        } // Agrega esta llave de cierre para corregir la estructura
        ?>

        <li class="Menu_secundario_li">
            <a href="ptn_pante_inicio_tco.php" class="nav-link ">
                <i class="bx bx-home-alt icon"></i>
            </a>
        </li>

        <?php
        if ($Reporte_General == 1) {
        ?>
            <li class="Menu_secundario_li">
                <a href="ptn_pante_consultas.php" class="nav-link">
                    <i class="fa-solid fa-print icon"></i>
                </a>
            </li>
        <?php
        } // Agrega esta llave de cierre para corregir la estructura
        ?>

        <?php
        if ($Servicios == 1) {
        ?>
            <li class="Menu_secundario_li">
                <a href="ptn_pante_servicios.php" class="nav-link">
                    <i class="fa-solid fa-chalkboard-user icon"></i>
                </a>
            </li>
        <?php
        } // Agrega esta llave de cierre para corregir la estructura
        ?>

        <?php
        if ($Panteones == 1) {
        ?>
            <li class="Menu_secundario_li">
                <a href="ptn_pante_panteones.php" class="nav-link">
                    <i class="fa-solid fa-church icon"></i>
                </a>
            </li>
        <?php
        } // Agrega esta llave de cierre para corregir la estructura
        ?>

        <li class="Menu_secundario_li">
            <a class="nav-link" href="#" data-toggle="modal" data-target="#Cerrar_sesion">
                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 icon"></i>
            </a>
        </li>
    </ul>

</div>