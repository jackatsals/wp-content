.fl-node-<?php echo $id; ?> .unity-modaal-gallery {
    grid-template-columns: <?php echo !empty($settings->layout_grid_responsive) ?$settings->layout_grid_responsive : '1'; ?>fr;
}

@media screen and (min-width: 768px) {
    .fl-node-<?php echo $id; ?> .unity-modaal-gallery {
        grid-template-columns: repeat(<?php echo !empty($settings->layout_grid_medium) ? $settings->layout_grid_medium : '2'; ?>, 1fr);
    }
}

@media screen and (min-width: 992px) {
    .fl-node-<?php echo $id; ?> .unity-modaal-gallery {
        grid-template-columns: repeat(<?php echo !empty($settings->layout_grid) ?$settings->layout_grid : '3'; ?>, 1fr);
    }
}
