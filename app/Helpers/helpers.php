<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Share;
use Illuminate\Support\Str;

if (!function_exists('get_current_action_view_type')) {
    /**
     * @param string $type
     * @return mixed
     */
    function get_current_action_view_type()
    {
        $type = '';

        $routeName = Route::currentRouteName();
        //get route root
        $routeRoot = explode('.', $routeName, 3)[0] . '.' . explode('.', $routeName, 3)[1];
        switch ($routeRoot) {
            case (DOCTOR_ROUTE_NAME_V1):
                $type = HOSPITAL_OR_DOCTOR_KEY_VALUE; //hospital or family
                break;

            case (FAMILY_ROUTE_NAME_V1):
                $type = FAMILY_KEY_VALUE; //family
                break;

            case (MEDICINE_ROUTE_NAME_V1):
                $type = FAMILY_KEY_VALUE; //medicine
                break;

                // case(FAMILY_ROUTE_NAME_V1):
                //     $type = FAMILY_KEY_VALUE; //keyword
                //     break;

                // case(FAMILY_ROUTE_NAME_V1):
                //     $type = FAMILY_KEY_VALUE; //medical condition
                //     break;



                // case(FAMILY_ROUTE_NAME_V1):
                //     $type = FAMILY_KEY_VALUE; //important word
                //     break;

            default:
                $type = HOSPITAL_OR_DOCTOR_KEY_VALUE;
        }

        return $type;
    }
}

if (!function_exists('generate_unique_code')) {
    /**
     * Write code on Method
     *
     * @return response()
     */
    function generate_unique_code()
    {
        do {
            $code = random_int(100000, 999999);
        } while (User::where("code", "=", $code)->first());

        return $code;
    }
}

if (!function_exists('generate_status')) {
    /**
     * Write code on Method
     *
     * @return response()
     */
    function generate_status()
    {
        do {
            $status = STATUS_REQUEST_VALUE;
        } while (Share::where("status", "=", $status)->first());

        return $status;
    }
}

if (!function_exists('concat_audio')) {
    /**
     * Write code on Method
     *
     * @return response()
     */
    function concat_audio($sounds = arr)
    {
        $sounds = collect($sounds);
        $input = "";
        $filter_complex = " -filter_complex '";
        $sound_path = storage_path('app/public/sounds/');
        if (!file_exists($sound_path)) mkdir($sound_path, 0775, true);

        $output = $sound_path . Str::random(30) . '.m4a';
        $sounds->each(function ($sound, $key) use (&$input, &$filter_complex) {
            $input .= ' -i ' . $sound;
            $filter_complex .= '[' . $key . ':a]';
        });
        $filter_complex .= "'concat=n=" . $sounds->count() . ":v=0:a=1 ";
        $ffmpeg_command = "ffmpeg" . $input . $filter_complex . $output;
        shell_exec($ffmpeg_command);
        $public_path = explode("app/public/", $output)[1];
        return $public_path;
    }
}
