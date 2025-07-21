<?php

use Carbon\Carbon;

if (!function_exists('formatarDataBR')) {
    /**
     * Formatar data no padrão brasileiro
     * 
     * @param Carbon|string $data
     * @param string $formato
     * @return string
     */
    function formatarDataBR($data, $formato = 'd/m/Y H:i')
    {
        if (!$data) {
            return '-';
        }
        
        $carbon = $data instanceof Carbon ? $data : Carbon::parse($data);
        return $carbon->format($formato);
    }
}

if (!function_exists('formatarDataBRCompleta')) {
    /**
     * Formatar data completa no padrão brasileiro
     * 
     * @param Carbon|string $data
     * @return string
     */
    function formatarDataBRCompleta($data)
    {
        if (!$data) {
            return '-';
        }
        
        $carbon = $data instanceof Carbon ? $data : Carbon::parse($data);
        return $carbon->format('d/m/Y H:i:s');
    }
}

if (!function_exists('formatarDataBRApenasData')) {
    /**
     * Formatar apenas a data no padrão brasileiro
     * 
     * @param Carbon|string $data
     * @return string
     */
    function formatarDataBRApenasData($data)
    {
        if (!$data) {
            return '-';
        }
        
        $carbon = $data instanceof Carbon ? $data : Carbon::parse($data);
        return $carbon->format('d/m/Y');
    }
}

if (!function_exists('tempoRestanteBR')) {
    /**
     * Retorna tempo restante em português
     * 
     * @param Carbon|string $data
     * @return string
     */
    function tempoRestanteBR($data)
    {
        if (!$data) {
            return '-';
        }
        
        $carbon = $data instanceof Carbon ? $data : Carbon::parse($data);
        return $carbon->diffForHumans();
    }
} 