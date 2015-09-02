<?php

namespace wptrebrets\inc;


class Install
{

    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'activate'));
        add_action('wptreb_daily_import', array($this, 'daily_grab'));
        add_action('wptreb_daily_clean', array($this, 'daily_clean'));
    }

    public function activate()
    {
        wp_schedule_event(time()+ (60 * 60 * 24), 'daily', 'wptreb_daily_import');
        wp_schedule_event(time() + (60*10) + (60 * 60 * 24), 'daily', 'wptreb_daily_clean');
    }

    public function daily_grab()
    {
        $get = new Commands();
        $get->getDaily();
    }

    public function daily_clean()
    {
        $get = new Commands();
        $expired = $get->verifyCurrent();
        $get->changeExpired($expired);
    }

}