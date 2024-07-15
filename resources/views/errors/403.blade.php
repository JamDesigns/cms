@extends('errors::layout')

@section('title', __('http-statuses.403'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: '403'))
