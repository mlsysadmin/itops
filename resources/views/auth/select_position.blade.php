<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Position</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-semibold text-center text-blue-600 mb-6">Select Your Position</h2>
        <form action="{{ route('position.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="position" class="block text-lg font-medium text-gray-700">Choose a position:</label>
                <select name="position" id="position" class="form-select w-full p-3 border border-gray-300 rounded-md" required>
                    <option value="">-- Select Position --</option>
                    @foreach($positions as $position)
                        <option value="{{ $position }}">{{ $position }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-md hover:bg-blue-500 transition">Continue</button>
        </form>
    </div>

</body>
</html>
