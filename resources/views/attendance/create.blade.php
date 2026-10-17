@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Mark attendance</h2>
            <div class="text-muted">{{ $classroom->name }}</div>
        </div>
        <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="{{ route('attendance.store', $classroom) }}">
                @csrf
                <div class="mb-4">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" id="date" name="date" class="form-control" value="{{ $date }}" required>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Student #</th>
                                <th>Present</th>
                                <th>Late</th>
                                <th>Absent</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->student_number }}</td>
                                    <td>
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="present" checked>
                                    </td>
                                    <td>
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="late">
                                    </td>
                                    <td>
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="absent">
                                    </td>
                                    <td>
                                        <input type="text" name="notes[{{ $student->id }}]" class="form-control" placeholder="Optional note">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button class="btn btn-primary btn-lg">Save attendance</button>
            </form>
        </div>
    </div>
</div>
@endsection
