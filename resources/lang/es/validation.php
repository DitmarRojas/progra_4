<?php

return [
    'accepted'             => 'El campo :attribute debe ser aceptado.',
    'accepted_if'          => 'El campo :attribute debe ser aceptado cuando :other es :value.',
    'active_url'           => 'El campo :attribute no es una URL válida.',
    'after'                => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal'       => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha'                => 'El campo :attribute solo debe contener letras.',
    'alpha_dash'           => 'El campo :attribute solo debe contener letras, números, guiones y guiones bajos.',
    'alpha_num'            => 'El campo :attribute solo debe contener letras y números.',
    'array'                => 'El campo :attribute debe ser un array.',
    'ascii'                => 'El campo :attribute solo debe contener caracteres alfanuméricos y símbolos de un solo byte.',
    'before'               => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal'      => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between'              => [
        'array'   => 'El campo :attribute debe tener entre :min y :max elementos.',
        'file'    => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string'  => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean'              => 'El campo :attribute debe ser verdadero o falso.',
    'can'                  => 'El campo :attribute contiene un valor no autorizado.',
    'confirmed'            => 'La confirmación de :attribute no coincide.',
    'current_password'     => 'La contraseña es incorrecta.',
    'date'                 => 'El campo :attribute no es una fecha válida.',
    'date_equals'          => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format'          => 'El campo :attribute no corresponde al formato :format.',
    'decimal'              => 'El campo :attribute debe tener :decimal decimales.',
    'declined'             => 'El campo :attribute debe ser rechazado.',
    'declined_if'          => 'El campo :attribute debe ser rechazado cuando :other es :value.',
    'different'            => 'El campo :attribute y :other deben ser diferentes.',
    'digits'               => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between'       => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions'           => 'El campo :attribute tiene dimensiones de imagen inválidas.',
    'distinct'             => 'El campo :attribute tiene un valor duplicado.',
    'doesnt_end_with'      => 'El campo :attribute no debe terminar con uno de los siguientes: :values.',
    'doesnt_start_with'    => 'El campo :attribute no debe comenzar con uno de los siguientes: :values.',
    'email'                => 'El campo :attribute debe ser una dirección de correo válida.',
    'ends_with'            => 'El campo :attribute debe terminar con uno de los siguientes valores: :values.',
    'enum'                 => 'El campo :attribute seleccionado no es válido.',
    'exists'               => 'El campo :attribute seleccionado no es válido.',
    'file'                 => 'El campo :attribute debe ser un archivo.',
    'filled'               => 'El campo :attribute debe tener un valor.',
    'gt'                   => [
        'array'   => 'El campo :attribute debe tener más de :value elementos.',
        'file'    => 'El archivo :attribute debe pesar más de :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'string'  => 'El campo :attribute debe tener más de :value caracteres.',
    ],
    'gte'                  => [
        'array'   => 'El campo :attribute debe tener :value elementos o más.',
        'file'    => 'El archivo :attribute debe pesar :value kilobytes o más.',
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'string'  => 'El campo :attribute debe tener :value caracteres o más.',
    ],
    'image'                => 'El campo :attribute debe ser una imagen.',
    'in'                   => 'El campo :attribute seleccionado no es válido.',
    'in_array'             => 'El campo :attribute debe existir en :other.',
    'integer'              => 'El campo :attribute debe ser un número entero.',
    'ip'                   => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4'                 => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6'                 => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json'                 => 'El campo :attribute debe ser una cadena JSON válida.',
    'lowercase'            => 'El campo :attribute debe estar en minúsculas.',
    'lt'                   => [
        'array'   => 'El campo :attribute debe tener menos de :value elementos.',
        'file'    => 'El archivo :attribute debe pesar menos de :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'string'  => 'El campo :attribute debe tener menos de :value caracteres.',
    ],
    'lte'                  => [
        'array'   => 'El campo :attribute no debe tener más de :value elementos.',
        'file'    => 'El archivo :attribute debe pesar :value kilobytes o menos.',
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'string'  => 'El campo :attribute debe tener :value caracteres o menos.',
    ],
    'mac_address'          => 'El campo :attribute debe ser una dirección MAC válida.',
    'max'                  => [
        'array'   => 'El campo :attribute no debe tener más de :max elementos.',
        'file'    => 'El archivo :attribute no debe pesar más de :max kilobytes.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string'  => 'El campo :attribute no debe ser mayor que :max caracteres.',
    ],
    'max_digits'           => 'El campo :attribute no debe tener más de :max dígitos.',
    'mimes'                => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes'            => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min'                  => [
        'array'   => 'El campo :attribute debe tener al menos :min elementos.',
        'file'    => 'El archivo :attribute debe pesar al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string'  => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'min_digits'           => 'El campo :attribute debe tener al menos :min dígitos.',
    'multiple_of'          => 'El campo :attribute debe ser múltiplo de :value.',
    'not_in'               => 'El campo :attribute seleccionado no es válido.',
    'not_regex'            => 'El formato del campo :attribute no es válido.',
    'numeric'              => 'El campo :attribute debe ser un número.',
    'password'             => [
        'letters'       => 'El campo :attribute debe contener al menos una letra.',
        'mixed'         => 'El campo :attribute debe contener al menos una letra mayúscula y una minúscula.',
        'numbers'       => 'El campo :attribute debe contener al menos un número.',
        'symbols'       => 'El campo :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'El campo :attribute ha aparecido en una filtración de datos. Por favor, elija otro :attribute.',
    ],
    'present'              => 'El campo :attribute debe estar presente.',
    'prohibited'           => 'El campo :attribute está prohibido.',
    'prohibited_if'        => 'El campo :attribute está prohibido cuando :other es :value.',
    'prohibited_unless'    => 'El campo :attribute está prohibido a menos que :other esté en :values.',
    'prohibits'            => 'El campo :attribute prohíbe que :other esté presente.',
    'regex'                => 'El formato del campo :attribute no es válido.',
    'required'             => 'El campo :attribute es obligatorio.',
    'required_array_keys'  => 'El campo :attribute debe contener entradas para: :values.',
    'required_if'          => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_if_accepted' => 'El campo :attribute es obligatorio cuando :other es aceptado.',
    'required_unless'      => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with'        => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all'    => 'El campo :attribute es obligatorio cuando :values están presentes.',
    'required_without'     => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de los valores :values están presentes.',
    'same'                 => 'El campo :attribute y :other deben coincidir.',
    'size'                 => [
        'array'   => 'El campo :attribute debe contener :size elementos.',
        'file'    => 'El archivo :attribute debe pesar :size kilobytes.',
        'numeric' => 'El campo :attribute debe ser :size.',
        'string'  => 'El campo :attribute debe tener :size caracteres.',
    ],
    'starts_with'          => 'El campo :attribute debe comenzar con uno de los siguientes valores: :values.',
    'string'               => 'El campo :attribute debe ser una cadena de caracteres.',
    'timezone'             => 'El campo :attribute debe ser una zona horaria válida.',
    'unique'               => 'El campo :attribute ya ha sido registrado.',
    'uploaded'             => 'El campo :attribute no se pudo subir.',
    'uppercase'            => 'El campo :attribute debe estar en mayúsculas.',
    'url'                  => 'El campo :attribute debe ser una URL válida.',
    'ulid'                 => 'El campo :attribute debe ser un ULID válido.',
    'uuid'                 => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Mensajes de validación personalizados
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar mensajes de validación personalizados para atributos usando la
    | convención "attribute.rule" para nombrar las líneas. Esto hace que sea rápido
    | especificar una línea de idioma personalizada específica para una regla de atributo dada.
    |
    */

    'custom' => [
        'rol_id' => [
            'required' => 'Debe seleccionar un rol.',
        ],
        'nombres' => [
            'regex' => 'El campo nombres solo puede contener letras y espacios.',
        ],
        'apellidos' => [
            'regex' => 'El campo apellidos solo puede contener letras y espacios.',
        ],
        // 'attribute-name' => [
        //     'rule-name' => 'custom-message',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atributos personalizados
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas de idioma se utilizan para intercambiar los marcadores de posición de atributos
    | por algo más amigable para el lector, como "Correo electrónico" en lugar de "email".
    |
    */

    'attributes' => [
        'rol_id' => 'rol', // Opcional: para que :attribute se reemplace por "rol" en otros mensajes
    ],
];