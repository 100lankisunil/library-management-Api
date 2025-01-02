<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

    <div class="container">
        <div>
            <div class="my-4 text-center">
                <h2>Books list</h2>
            </div>
            <table class="table">
                <div>
                    <select id="bookFilter">
                        <option value="all" selected>All</option>
                        <option value="available">Available</option>
                        <option value="borrowed">Borrowed</option>
                        <option value="returned">Returned</option>
                    </select>
                </div>
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Title</th>
                        <th scope="col">Description</th>
                        <th scope="col">Status</th>
                        <th scope="col">Borrowed By </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($borrowed as $book)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $book->title }}</td>
                        <td>{{ $book->description }}</td>
                        <td>{{ $book->status }}</td>
                        <td>{{ $book->status =="available" ? "-" : $book->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {
        $(document).on("change", "#bookFilter", function() {
            var selectedValue = $(this).val();
            // console.log(selectedValue);

            $.ajax({
                type: "GET"
                , url: "http://localhost:8000/api/books/available"
                , data: {
                    "filter": selectedValue
                }
                , success: function(data) {
                    if (data.status === "true") {
                        // Clear existing table rows
                        const tableBody = document.querySelector('table tbody');
                        tableBody.innerHTML = '';

                        // Populate table with new data
                        data.borrowed.forEach((book, index) => {
                            const row = `
                        <tr>
                            <th scope="row">${index + 1}</th>
                            <td>${book.title}</td>
                            <td>${book.description}</td>
                            <td>${book.status || 'N/A'}</td>
                            <td>${book.status === 'available' ? '-' : (book.name || '-')}</td>
                        </tr>`;
                            tableBody.innerHTML += row;
                        });
                    } else {
                        console.error('Error: ', data.message);
                    }
                }
                , error: function(xhr, status, error) {
                    console.error('AJAX Error: ', xhr.responseText);
                }
            });
        });
    });

</script>

</html>
