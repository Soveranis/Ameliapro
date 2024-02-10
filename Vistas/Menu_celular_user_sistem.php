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
        <li class="Menu_secundario_li">
            <a href="ptn_sist_inicio.php" class="nav-link ">
                <i class="bx bx-home-alt icon"></i>
            </a>
        </li>
        <li class="Menu_secundario_li">
            <a href="ptn_sist_usuarios.php" class="nav-link">
                <i class="fa-regular fa-user icon"></i>
            </a>
        </li>
        <li class="Menu_secundario_li">
            <a href="ptn_sist_logos.php" class="nav-link">
                <i class="fa-regular fa-folder-open icon"></i>
            </a>
        </li>

        <li class="Menu_secundario_li">
            <a href="#" class="nav-link" data-toggle="modal" data-target="#Cerrar_sesion">
                <i class="bx bx-log-out icon"></i>
            </a>
        </li>
    </ul>

</div>