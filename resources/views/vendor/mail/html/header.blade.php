@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<span style="color: #2563eb; font-weight: 900; font-size: 28px;">HRD<span style="color: #0f172a;">Apps</span></span>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
