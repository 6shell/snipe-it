@component('mail::message')
{{ trans_choice('mail.assets_warrantee_alert', $assets->count(), ['count'=>$assets->count(), 'threshold' => $threshold]) }}
@component('mail::table')

{{--<table width="100%">--}}
{{--<tr><td>&nbsp;</td><td>{{ trans('mail.name') }}</small></td><td>{{ trans('mail.serial') }}</td><td>{{ trans('mail.Days') }}</td><td>{{ trans('mail.expires') }}</td><td>{{ trans('mail.supplier') }}</td><td>{{ trans('mail.assigned_to') }}</td></tr>--}}
@foreach ($assets as $asset)
@php
$expires = Helper::getFormattedDateObject($asset->present()->warranty_expires, 'date');
$diff = round(abs(strtotime($asset->present()->warranty_expires) - strtotime(date('Y-m-d')))/86400);
$icon = ($diff <= ($threshold / 2)) ? '🚨' : (($diff <= $threshold) ? '⚠️' : ' ');
@endphp
        {{--<tr><td>{{ $icon }} </td><td> <a href="{{ route('hardware.show', $asset->id) }}">{{ $asset->present()->name }}</a><br><small>{{trans('mail.serial').': '.$asset->serial}}</small></td><td> {{ $diff }} {{ trans('mail.Days') }} </td><td> {{ !is_null($expires) ? $expires['formatted'] : '' }} </td><td> {{ ($asset->supplier ? e($asset->supplier->name) : '') }} </td><td> {{ ($asset->assignedTo ? e($asset->assignedTo->present()->display_name) : '') }} </td></tr>--}}
@component('mail::table')
|        |          |
| ------------- | ------------- |
| **{{ trans('mail.name') }}** | <a href="{{ route('hardware.show', $asset->id) }}">{{ $asset->present()->name }}</a> <br><small>{{trans('mail.serial').': '.$asset->serial}}</small> |
| **{{ trans('mail.expires') }}** | {{ !is_null($expires) ? $expires['formatted'] : '' }} ( {{ $icon }} {{ $diff }} {{ trans('mail.Days') }} ) |
@if ($asset->supplier)
| **{{ trans('mail.supplier') }}** | {{ ($asset->supplier ? e($asset->supplier->name) : '') }} |
@endif
@if ($asset->assignedTo)
| **{{ trans('mail.assigned_to') }}** | {{ e($asset->assignedTo->present()->display_name) }} |
@endif
@endcomponent
@endforeach
{{--</table>--}}
@endcomponent
@endcomponent
