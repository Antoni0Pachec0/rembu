<?php require_once "./app/views/inc/navbar.php"; ?>

<!-- Requiriendo estilos -->
<link rel="stylesheet" href="<?php echo APP_URL; ?>/app/views/css/content/inicio.css">

<main>
    <section class="contentPages" id="cpUno">
        <div class="contentClass" id="element1">
            <div id="titulo">

                <div id="marca"></div>

                <p><b>Transformando Ciudades,<br>
                        Monitoreando el Futuro
                    </b></p>
            </div>
            <div class="textos" id="contenido">
                <p>REMBU es una plataforma innovadora que une tecnología y naturaleza para crear ciudades más resilientes y sostenibles. <br>
                    Con nuestra red de evaluación y monitoreo para bosques urbanos, estamos abordando la crisis climática de manera tangible y efectiva. <br>
                </p>
            </div>
        </div>

        <div class="contentClass" id="element2">
            <div id="imgAntena">
                <img src="<?php echo APP_URL; ?>app/views/img/principal.png" alt="">
            </div>
        </div>
    </section>

    <section class="contentPages" id="cpInsert">

        <div id="titulo">
            <h1>Únete a la Revolución Ambiental de REMBU</h1>
        </div>

        <div class="textos" id="texto">
            <p>
                Mejora la calidad del aire. Promueve el turismo inclusivo. Mitiga el cambio climático. Únete a nosotros y sé parte del cambio.
            </p>
        </div>

        <div id="botones">
            <a href="#">Login</a>
            <a href="#">Sign Up</a>
        </div>

        <div class="textos" id="texto2">
            <p>
                ¡Regístrate ahora y descubre cómo puedes contribuir al futuro de tu ciudad!
            </p>
        </div>

    </section>

    <section class="contentPages" id="cpDos">

        <div id="tituloDos">
            <p>Condición Atmosférica</p>
        </div>

        <div id="contentMaps">

            <div class="elementosMaps" id="elementoUno">
                <video autoplay loop muted src="<?php echo APP_URL; ?>app/views/img/videoPrue.mp4"></video>
            </div>

            <div class="elementosMaps" id="elementoDos">

                <table border="1px">
                    <thead>
                        <tr>
                            <th colspan="5">Datos Brutos</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Temp</td>
                            <td>Noise</td>
                            <td>Humidity</td>
                            <td>CO2</td>
                            <td>PM10</td>
                        </tr>
                        <tr>
                            <td>Temp</td>
                            <td>Noise</td>
                            <td>Humidity</td>
                            <td>CO2</td>
                            <td>PM10</td>
                        </tr>
                        <tr>
                            <td>Temp</td>
                            <td>Noise</td>
                            <td>Humidity</td>
                            <td>CO2</td>
                            <td>PM10</td>
                        </tr>
                        <tr>
                            <td>Temp</td>
                            <td>Noise</td>
                            <td>Humidity</td>
                            <td>CO2</td>
                            <td>PM10</td>
                        </tr>
                        <tr>
                            <td>Temp</td>
                            <td>Noise</td>
                            <td>Humidity</td>
                            <td>CO2</td>
                            <td>PM10</td>
                        </tr>
                        <tr>
                            <td>Temp</td>
                            <td>Noise</td>
                            <td>Humidity</td>
                            <td>CO2</td>
                            <td>PM10</td>
                        </tr>
                        <tr>
                            <td>Temp</td>
                            <td>Noise</td>
                            <td>Humidity</td>
                            <td>CO2</td>
                            <td>PM10</td>
                        </tr>
                        <tr>
                            <td>Temp</td>
                            <td>Noise</td>
                            <td>Humidity</td>
                            <td>CO2</td>
                            <td>PM10</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

    <section class="contentPages" id="cpTres">

        <div id="titulo">
            <h2>Prototipo</h2>
        </div>

        <div class="contentClass">
            <div class="content" id="contentIzq">
                <img src="<?php echo APP_URL; ?>app/views/img/rembu2.png" alt="">

                
            </div>

            <div class="content" id="contentDer">

            </div>
        </div>
    </section>







    <section class="contentPages" id="cpTres">

        <div class="contentClass" id="element1">
            <div id="imgAntena">
            </div>
        </div>

        <div class="contentClass" id="element2">

            <div class="elements"></div>
            <div class="elements"></div>

        </div>
    </section>
</main>

<?php require_once "./app/views/inc/footer.php"; ?>