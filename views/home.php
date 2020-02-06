<div class="container">
    <div class="row margin-home mt-4 mb-4">
        <div class="col-sm-10 offset-sm-1 col-md-8 offset-md-2">
            <h2>Most rainfall in the world:</h2>
            <div class="row">
                <div class="col-sm-8">
                    <p><span id="current"><strong>Realtime Top 10 information</strong></span></p>
                </div>
                <div class="col-sm-4">
                    <?php if(isset($priv_level) && $priv_level >= 1): ?>
                        <?php if(isset($hasHistorical) && $hasHistorical): ?>
                            <a style="float: right;" class="btn btn-primary mt-n2 mb-n2 mr-1" href="/assets/historical/<?= date('Y-m-d') ?>.xml" target="_blank" download>
                                Historical Data
                                <img src="/assets/download-icon.svg" width="15px" height="15px" alt="download icon">
                            </a>
                        <?php else: ?>
                            <button style="float: right;" class="btn btn-secondary mt-n2 mb-n2 mr-1" disabled>
                                Historical Data
                                <img src="/assets/download-icon.svg" width="15px" height="15px" alt="download icon">
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Station</th>
                        <th scope="col">Country</th>
                        <th scope="col">Rainfall</th>
                    </tr>
                </thead>
                <tbody id="top-ten">
                    <!--     Data generated in JS       -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="/assets/topten.js"></script>