<div class="flex items-center justify-center space-x-2">
    <a href="{{ route('tasks.show', $task->id) }}"
       class="text-indigo-600 hover:text-indigo-900 transition-colors"
       title="View">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('tasks.edit', $task->id) }}"
       class="text-blue-600 hover:text-blue-900 transition-colors"
       title="Edit">
        <i class="fas fa-edit"></i>
    </a>
    <button onclick="deleteTask({{ $task->id }}, '{{ $task->title }}')"
            class="text-red-600 hover:text-red-900 transition-colors"
            title="Delete">
        <i class="fas fa-trash"></i>
    </button>
</div>
