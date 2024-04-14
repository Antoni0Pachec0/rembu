<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/css/inc/navbar.css">

<nav>

    <div class="spaceNav" id="spaceNavUno">
        <img id="logoImg" src="<?php echo APP_URL;?>app/views/img/rembuImg.png" alt="">
        <div id="textos">
            <h3>REMBU</h3>
            <p>(Red de Evaluación y Monitoreo de Bosques Urbanos)</p>
        </div>
    </div>

    <!--
    <div id="opcionCero">
        <ul>
            <li><a href="https://www.google.com/"></a>inicio</li>
            <li><a href=""></a>inicio2</li>
            <li><a href="https://www.google.com/"></a>inicio3</li>
            <li><a href=""></a>inicio4</li>
        </ul>
    </div>

    !-->

    <div class="spaceNav" id="spaceNavDos">
        <div id="marcas">
            <a href="<?php echo APP_URL; ?>inicio/">Inicio</a>
            <a href="<?php echo APP_URL; ?>inicio/">Registro</a>
            <a href="<?php echo APP_URL; ?>inicio/">Admosfera</a>
        </div>

        <select name="menuUno" id="menuUno" onchange="redirect()">
            <option value="">Proyect Information</option>
            <option value="option1">Proyect Description</option>
            <option value="opcion 2">GitHub</option>
            <option value="opcion 3">Team Members</option>
            <option value="opcion 3">Video Demo</option>

        </select>
    </div>

    <div class="spaceNav" id="spaceNavTres"> 
        <button>Buy a Miner</button>
        <button>Data Viewer</button>
    </div>

</nav>

<script src="<?php echo APP_URL;?>app/views/inc/js/option.js"></script>