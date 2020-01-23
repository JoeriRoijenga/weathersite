<!DOCTYPE html>
<html lang="en">
<head>
    <title>Icon Symbolizer</title>
    <!-- The line below is only needed for old environments like Internet Explorer and Android 4.x -->
    <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.5/ol.css">

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <style>
        body  {
             height: 100%;
         }
        html {
            height: 100%;
        }
        .full-screen {
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="container full-screen">
        <div class="row full-screen">
            <div class="col-sm-6 full-screen">
                <div id="map" class="map full-screen"><div id="popup"></div></div>
                <input type="hidden" id="currentStation" value="0" onchange="startChart()">
            </div>
            <div class="col-sm-6" id="graph">
                <div class="row">
                    <canvas id="myChart"></canvas>
                </div>
                <div class="row">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">First</th>
                            <th scope="col">Last</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <?php
                            for ($i = 1; $i<=10; $i++) {
                                ?>
                                <tr>
                                    <th scope="row"><?php $i ?></th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.5/ol.js"></script>
    <script src="index.js"></script>
    <script src="graph.js"></script>
</body>
</html>