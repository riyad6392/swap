<?php

if (! function_exists('retrieveMessage')) {
    //Format a message for data retrieval.
    function retrieveMessage(string $model) {
        return $model . ' Retrieved Successfully';
    }
}

if (! function_exists('updateMessage')) {
    //Format a message for data update.
    function updateMessage(string $model) {
        return $model . ' Updated Successfully';
    }
}

if (! function_exists('deleteMessage')) {
    //Format a message for data delete.
    function deleteMessage(string $model) {
        return $model . ' Delete Successfully';
    }
}

if (! function_exists('sentMessage')) {
    //Format a message for data sent.
    function sentMessage(string $model) {
        return $model . ' Sent Successfully';
    }
}

if (! function_exists('retrieveMessage')) {
    //Format a message for data retrieval.
    function retrieveMessage(string $model) {
        return $model . ' Retrieved Successfully';
    }
}

