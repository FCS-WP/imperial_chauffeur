<?php
        function disable_password_reset() {
            return false;
        }
        add_filter( 'allow_password_reset', 'disable_password_reset' );
