<?php
function sanitize_checkbox( $value ) {
    return isset( $value ) && $value === 'on' ? 1 : 0;
}