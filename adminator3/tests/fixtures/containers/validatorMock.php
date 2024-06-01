<?php

$container->set(
    'validator',
    function ($container) {
        // return new App\Validation\Validator();

        $validator = \Mockery::mock(
            App\Validation\Validator::class,
        );
        // $sentinel->shouldReceive('getUser')->andReturn($userObj);

        return $validator;
    }
);
