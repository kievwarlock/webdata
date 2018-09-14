<?php if (is_array($array_point_by_type) and $token ) { ?>

    <div class="profile-geo-points-inner custom-tab-style">


        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">

            <?php
            $cnt = 0;
            foreach ($array_point_by_type as $geo_type_key => $geo_type_points) {
                $cnt++;
                $active = '';
                if ($cnt == 1) {
                    $active = 'active';
                }
                ?>
                <li role="presentation" class="btn-sm <?= $active ?>">
                    <a href="#<?= $geo_type_key ?>" aria-controls="home" role="tab" data-toggle="tab">
                        <?= $geo_type_key ?>
                    </a>
                </li>
            <?php } ?>

        </ul>

        <!-- Tab panes -->
        <div class="tab-content">


            <?php
            $cnt = 0;
            foreach ($array_point_by_type as $geo_type_key => $geo_type_points) {
                $cnt++;
                $active = '';
                if ($cnt == 1) {
                    $active = 'active';
                }
                ?>


                <div role="tabpanel" class="tab-pane <?= $active ?>" id="<?= $geo_type_key ?>">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th class="hidden-xs hidden-sm">
                                ID
                            </th>
                            <th class="hidden-xs hidden-sm">
                                latitude
                            </th>
                            <th class="hidden-xs hidden-sm">
                                longitude
                            </th>
                            <th scope="col">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (is_array($geo_type_points)) {
                            foreach ($geo_type_points as $point) { ?>
                                <tr>
                                    <td class="hidden-xs hidden-sm">
                                        <?= ($point['id']) ? $point['id'] : 'NULL' ?>
                                    </td>
                                    <td class="hidden-xs hidden-sm">
                                        <?= ($point['latitude']) ? $point['latitude'] : 'NULL' ?>
                                    </td>
                                    <td class="hidden-xs hidden-sm">
                                        <?= ($point['longitude']) ? $point['longitude'] : 'NULL' ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-xs btn-success view-point-data"
                                                    data-type="<?= $geo_type_key ?>"
                                                    data-point="<?= $point['id'] ?>"
                                                    data-token="<?= $token ?>"
                                                    aria-label="Left Align">
                                                <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                                                <span class="hidden-xs hidden-sm">View</span>
                                            </button>
                                        </div>

                                    </td>
                                </tr>
                            <?php }
                        }
                        ?>

                        </tbody>
                    </table>
                </div>
            <?php } ?>


        </div>

    </div>


<?php }else{
    echo 'NO geo points !';
} ?>