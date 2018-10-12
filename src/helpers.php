<?php

use Crebs86\Acl\Controllers\Util\ControlAccess;
use Carbon\Carbon;

if (!function_exists('have')) {
    /**
     * @param $permission
     * @param bool $needSuperAdmin
     * @return void
     */
    function have($permission, $needSuperAdmin = true)
    {
        $have = new ControlAccess();
        $have->check($permission, $needSuperAdmin)->on();
    }
}

if (!function_exists('can')) {
    /**
     * @param $permission
     * @param bool $needSuperAdmin
     * @return bool
     */
    function can($permission, $needSuperAdmin = true)
    {
        $can = new ControlAccess();
        return $can->check($permission, $needSuperAdmin)->get();
    }
}

if (!function_exists('own')) {
    /**
     * @param $permission
     * @param $collection
     * @return void
     */
    function own($permission, $collection)
    {
        $isOwn = new ControlAccess();
        $isOwn->check($permission)->self($collection);
    }
}
if (!function_exists('isOwn')) {
    /**
     * @param $permission
     * @param $collection
     * @return bool
     */
    function isOwn($permission, $collection)
    {
        $isOwn = new ControlAccess();
        return $isOwn->check($permission)->isOwn($collection);
    }
}
if (!function_exists('requireValidEmail')) {
    /**
     * @return bool
     */
    function requireValidEmail()
    {
        $setting = new \Crebs86\Acl\Controllers\ControlPanel\Setting();
        return $setting->getDBSettings(['validate_mail'])->cantDo();
    }
}

if (!function_exists('crumbs')) {
    function crumbs()
    {
        return new \Crebs86\Acl\Controllers\Util\Misc();
    }
}

if (!function_exists('run')) {
    function run($class)
    {
        switch ($class) {
            case "testes":
                return new \App\Http\Controllers\Testes();
            case "access":
                return new ControlAccess();
            case "bc":
                return new \Crebs86\Acl\Controllers\Util\Misc();
        }
    }
}

if (!function_exists('configAgenda')) {
    function configAgenda($fator = false, array $config)
    {
        return $fator ? $config[0] : $config[1];
    }
}

if (!function_exists('data')) {
    function data($data)
    {
        if (!(is_null($data))) {
            $data = preg_replace('[/]', '-', $data);
            $data = date('Y-m-d', strtotime($data));
            return $data;
        }
        return null;
    }
}

if (!function_exists('legenda')) {
    /**
     * @param $ativo
     * @param $transferida
     * @param $falta
     * @param $atendida
     * @return array
     * Define cores de fonte e background para legenda de demonstração
     */
    function legenda($ativo, $transferida, $falta, $atendida)
    {
        if ($atendida) {
            return ['backgroundColor' => 'green'];
        }
        if ($falta) {
            return [
                'backgroundColor' => 'red',
                'textColor' => 'white'
            ];
        }
        if ($transferida) {
            return ['backgroundColor' => 'deeppink'];
        }
        if (!$ativo) {
            return [
                'backgroundColor' => 'gray',
                'textColor' => 'white'
            ];
        }
        return [
            'backgroundColor' => '#0056b3'
        ];
    }
}
if (!function_exists('build')) {
    /**
     * @param $nome
     * @param $inicio
     * @param $fim
     * @param $url
     * @param $ativo
     * @param $transferida
     * @param $falta
     * @param $atendida
     * @return array
     * Recebe dados do agendamento e define comportamento do calendário
     */
    function build($nome, $inicio, $fim, $url, $ativo, $transferida, $falta, $atendida)
    {
        $arr = [
            'title' => $nome,
            'start' => $inicio,
            'end' => $fim,
            'url' => $url,
        ];
        return array_merge($arr, legenda($ativo, $transferida, $falta, $atendida));
    }
}
if (!function_exists('selectClasse')) {
    /**
     * @return string
     */
    function selectClasse($class = "")
    {
        $classes = \App\Classe::all('label', 'id');
        $select = "<select class='{$class}' id='classe' name='classe'><option selected></option>";
        foreach ($classes as $classe) {
            $select .= "<option value='{$classe->id}'>{$classe->label}</option>";
        }
        $select .= "</select>";
        return $select;
    }
}
if (!function_exists('selectProfissionais')) {
    /**
     * @return string
     */
    function selectProfissionais($class = "", $classe)
    {
        $profisionais = \Illuminate\Support\Facades\DB::table('classes')
            ->select('name', 'users.id')
            ->where('classes.id', $classe)
            ->join('profissional_por_classes', 'profissional_por_classes.classe_id', '=', 'classes.id')
            ->join('users', 'users.id', '=', 'profissional_por_classes.user_id')
            ->get();

        if ($profisionais->count() == 0) {
            return '<p class="alert alert-danger">Nenhum profissional cadastrado para esta classe</p>';
        }
        $select = "<hr><select class='{$class}' id='profissional' name='profissional'><option selected>selecione profissional...</option>";
        foreach ($profisionais as $profisional) {
            $select .= "<option value='{$profisional->id}'>{$profisional->name}</option>";
        }
        $select .= "</select>";
        $select .= '<hr><a href="#" role="button" class="btn btn-outline-success popover-test form-control" title="Popover title" id="abrirAgenda" onclick="getFormAgenda(';
        $select .= '\'' . route('agenda-form') . '\',';
        $select .= '\'' . csrf_token() . '\')"';
        $select .= '>Abrir Agenda</a>';
        return $select;
    }
}
if (!function_exists('getFormAgenda')) {
    /**
     * @return string
     */
    function getFormAgenda()
    {
        $form = '<div class="form-group col-sm-12"><br>
    <input class="form-control" type="date" name="data" id="data">
</div>
<div class="form-group col-sm-6">
    <input class="form-control" type="time" name="inicio" id="inicio" required>
</div>
<div class="form-group col-sm-6">
    <input class="form-control" type="time" name="fim" id="fim" required>
</div>
<div class="form-group col-sm-12">
    <input class="form-control" type="number" name="quantidade" value="1" id="quantidade" required>
</div>
<hr>
<a class="btn btn-success form-control" href="#" onclick="gerarHorarios(\'' . route('agenda-horarios') . '\',\'' . csrf_token() . '\')">Gerar
    Horários</a>
</div>';
        return $form;
    }
}

if (!function_exists('horarioPadrao')) {
    function horarioPadrao($dataHora)
    {
        return Carbon::parse($dataHora)->format('Y-m-d H:i');
    }
}
if (!function_exists('horarioDefinido')) {

    function horarioDefinido($formatoDefinido, $dataHora)
    {
        return Carbon::parse($dataHora)->format($formatoDefinido);
    }
}