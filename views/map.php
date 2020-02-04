<div class="container full-screen">
    <div class="row full-screen">
        <div class="col-sm-6 full-screen">
            <div id="map" class="map full-screen"><div id="popup"></div></div>
            <input type="hidden" id="currentStation" value="0" onchange="startChart()">
        </div>
        <div class="col-sm-6" id="graph">
            <div class="row">
                <div id="labelChart col-md-12"><h1 id="stationName">No Station Chosen</h1></div>
            </div>
            <div class="row">
                <section id="tabs" class="project-tab">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <nav>
                                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Line Graph</a>
                                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Bar Graph</a>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                        <canvas id="lineGraph"></canvas>
                                    </div>
                                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                        <canvas id="barGraph"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript files -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.5/ol.js"></script>
<script src="/assets/map/map.js"></script>
<script src="/assets/map/graph.js"></script>