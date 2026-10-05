@php($present = \Nsd7\LaravelActivitylogUi\Support\ActivityPresenter::class)
@php($hasOld = collect($changes)->contains(fn ($row) => $row['status'] !== 'added'))

<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
    <table class="min-w-full divide-y divide-slate-200 text-xs dark:divide-slate-800">
        <thead class="bg-slate-50 text-left font-medium uppercase tracking-wide text-slate-500 dark:bg-slate-900/60 dark:text-slate-400">
            <tr>
                <th scope="col" class="px-3 py-2">Field</th>
                @if ($hasOld)
                    <th scope="col" class="px-3 py-2">Old</th>
                @endif
                <th scope="col" class="px-3 py-2">New</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-mono dark:divide-slate-800">
            @foreach ($changes as $row)
                <tr class="{{ $row['status'] === 'unchanged' ? 'opacity-60' : '' }}">
                    <td class="whitespace-nowrap px-3 py-2 font-sans font-medium text-slate-700 dark:text-slate-300">{{ $row['key'] }}</td>
                    @if ($hasOld)
                        <td class="px-3 py-2 {{ $row['status'] === 'changed' || $row['status'] === 'removed' ? 'bg-rose-50 text-rose-800 dark:bg-rose-500/10 dark:text-rose-300' : 'text-slate-500' }}">
                            <span class="break-all">{{ $row['status'] === 'added' ? '' : $present::formatValue($row['old']) }}</span>
                        </td>
                    @endif
                    <td class="px-3 py-2 {{ $row['status'] === 'changed' || $row['status'] === 'added' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300' : 'text-slate-500' }}">
                        <span class="break-all">{{ $row['status'] === 'removed' ? '' : $present::formatValue($row['new']) }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
