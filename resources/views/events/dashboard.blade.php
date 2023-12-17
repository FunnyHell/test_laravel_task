@extends('adminlte::page')

@section('title', 'Dashboard')

{{-- Переопределение навигационной панели --}}
@section('navbar')
    {{-- Имя пользователя и кнопка выхода --}}
    <li class="nav-item d-none d-sm-inline-block">
        {{--        <a href="#" class="nav-link">{{ Auth::user()->name }}</a>--}}
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">Выйти</a>
    </li>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <!-- Event Details -->
                <div class="card card-primary" id="event-details">
                    <!-- Event details will be loaded here dynamically -->
                </div>
            </div>
        </div>
    </div>

    <div id="modalOverlay" class="modal justify-content-center align-content-center" onclick="closeModalOutside(event)">

        <div id="participantModal" style="display: none;" class="modal-dialog-centered">
            <div id="participantDetails" class="modal-content bg-white p-3">
                <!-- Participant details will be loaded here dynamically -->
            </div>
        </div>
    </div>

@stop

@push('js')
    <script>
        let selectedEventId = null;

        function closeModalOutside(event) {
            if (event.target.id === 'modalOverlay') {
                closeModal();
            }
        }

        function updateParticipantsList(selectedEventId, token) {
            fetch(`/api/events/${eventId}`, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    const participants = data.result.participants;
                    let participantsHtml = participants.map(participant =>
                        `<li onclick="showParticipantDetails(${participant.id}, '${token}')">${participant.first_name} ${participant.last_name}</li>`
                    ).join('');
                    document.getElementById('event-participants').innerHTML = `<ul>${participantsHtml}</ul>`;
                })
                .catch(error => console.error('Ошибка:', error));
        }

        document.addEventListener('DOMContentLoaded', function () {
            const token = localStorage.getItem('auth_token');
            // Load user info and events
            if (token) {
                updateUserBlock(token);
                updateEventsList(token);
            } else {
                showLoginRegister();
            }

            // Function to handle logout
            window.logout = function () {
                localStorage.removeItem('auth_token');
                window.location.href = '{{ route('login') }}';
            };

            // Refresh events and participants every 30 seconds
            setInterval(function () {
                if (token) {
                    updateEventsList(token);
                }
                if (selectedEventId) {
                    updateParticipantsList(selectedEventId, token);
                }
            }, 30000);
        });

        function updateUserBlock(token) {
            fetch('/api/user', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(user => {
                    document.querySelector('.user-block').innerHTML = `
            <h1 class="text-center text-white-50">${user.first_name} ${user.last_name}</h1>
            <button onclick="logout()" class="btn btn-danger btn-block">Выйти</button>
        `;
                })
                .catch(error => console.error('Error:', error));
        }

        function updateEventsList(token) {
            // Запрос на получение всех событий
            fetch('/api/events', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(events => {
                    const eventsList = events.result.map(event =>
                        `<li class="nav-item">
                <a href="#" class="nav-link" onclick="selectEvent(${event.id}, '${token}')">${event.title}</a>
            </li>`
                    ).join('');
                    document.getElementById('all-events').innerHTML = eventsList;
                })
                .catch(error => console.error('Error:', error));

            // Запрос на получение событий текущего пользователя
            fetch('/api/user/events', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(userEvents => {
                    const userEventsList = userEvents.result.map(event =>
                        `<li class="nav-item">
                <a href="#" class="nav-link" onclick="selectEvent(${event.id}, '${token}')">${event.title}</a>
            </li>`
                    ).join('');
                    document.getElementById('my-events').innerHTML = userEventsList;
                })
                .catch(error => console.error('Error:', error));
        }

        function showLoginRegister() {
            document.querySelector('.user-block').innerHTML = `
        <a href="{{ route('login') }}" class="btn btn-primary btn-block">Войти</a>
        <a href="{{ route('register') }}" class="btn btn-success btn-block">Зарегистрироваться</a>
    `;
        }

        function showErrorModal(error) {
            alert(error);
        }

        function participateInEvent(eventId, token) {
            fetch(`/api/events/${eventId}/participate`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showErrorModal(data.error);
                    } else {
                        // Обновление списка моих событий
                        updateEventsList(token);
                        // Обновление списка участников
                        selectEvent(eventId, token);
                        // Поменять кнопку на "Отказаться от участия"
                        document.getElementById('actionButton').innerHTML = `<button onclick="withdrawFromEvent(${eventId}, '${token}')">Отказаться от участия</button>`;
                    }
                })
                .catch(error => showErrorModal(error));
        }

        function withdrawFromEvent(eventId, token) {
            fetch(`/api/events/${eventId}/withdraw`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showErrorModal(data.error);
                    } else {
                        // Обновление списка моих событий
                        updateEventsList(token);
                        // Обновление списка участников
                        selectEvent(eventId, token);
                        // Поменять кнопку на "Принять участие"
                        document.getElementById('actionButton').innerHTML = `<button onclick="participateInEvent(${eventId}, '${token}')">Принять участие</button>`;
                    }
                })
                .catch(error => showErrorModal(error));
        }

        function selectEvent(eventId, token) {
            selectedEventId = eventId;
            // Сначала снимаем выделение со всех элементов списка событий
            document.querySelectorAll('#all-events a, #my-events a').forEach(link => {
                link.classList.remove('active');
            });

            // Выделяем выбранный элемент
            const selectedEventLink = document.querySelector(`a[onclick="selectEvent(${eventId}, '${token}')"]`);
            if (selectedEventLink) {
                selectedEventLink.classList.add('active');
            }

            // Получаем данные о событии с сервера
            fetch(`/api/events/${eventId}`, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка при запросе данных о событии');
                    }
                    return response.json();
                })
                .then(event => {
                    event = event.result;
                    // Обновляем HTML основной части страницы данными события
                    const eventDetailsHtml = `
            <div class="card-header">
                <h3 class="card-title">${event.title}</h3>
            </div>
            <div class="card-body">
                <p><strong>Описание:</strong> ${event.text}</p>
                <p><strong>Дата создания:</strong> ${new Date(event.creation_date).toLocaleString()}</p>

                <div id="event-participants">
                    <p><strong>Участники:</strong></p>
                </div>
            </div>
        `;
                    document.getElementById('event-details').innerHTML = eventDetailsHtml;
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                });
            fetch(`/api/events/${eventId}`, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    let event = data.result.event;
                    let user = data.result.user;
                    let participants = data.result.participants;
                    let isUserCreator = participants.some(participant => participant.id === user.id);
                    ;
                    // Формируем HTML для списка участников
                    let participantsHtml = participants.map(participant =>
                        `<li onclick="showParticipantDetails(${participant.id}, '${token}')">${participant.first_name} ${participant.last_name}</li>`
                    ).join('');

                    let actionButtonHtml = isUserCreator ?
                        `<div id="actionButton">
                            <button onclick="withdrawFromEvent(${eventId}, '${token}')">Отказаться от участия</button>
                        </div>` :
                        `<div id="actionButton">
                            <button onclick="participateInEvent(${eventId}, '${token}')">Принять участие</button>
                        </div>`;

                    let eventDetailsHtml = `
            <div class="card-header">
                <h3 class="card-title">${event.title}</h3>
            </div>
            <div class="card-body">
                <p><strong>Описание:</strong> ${event.text}</p>
                <p><strong>Дата создания:</strong> ${new Date(event.creation_date).toLocaleString()}</p>
                <div id="event-participants">
                    <p><strong>Участники:</strong></p>
                    <ul>${participantsHtml}</ul>
                </div>
                ${actionButtonHtml}
            </div>
        `;
                    document.getElementById('event-details').innerHTML = eventDetailsHtml;
                })
                .catch(error => console.error('Ошибка:', error));

        }

        function showParticipantDetails(participantId, token) {
            fetch(`/api/user/${participantId}`, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    const participant = data.result;
                    let detailsHtml = `
            <p>Имя: ${participant.first_name}</p>
            <p>Фамилия: ${participant.last_name}</p>
        `;
                    const birthdate = '';
                    if (participant.birthdate) {
                        detailsHtml += `
            <p>Дата рождения: ${new Date(participant.birthdate).toLocaleDateString()}</p>
        `;
                    }
                    document.getElementById('participantDetails').innerHTML = detailsHtml;
                    openModal();
                })
                .catch(error => console.error('Ошибка:', error));
        }

        function openModal() {
            document.getElementById('participantModal').style.display = 'flex';
            document.getElementById('modalOverlay').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modalOverlay').style.display = 'none';
            document.getElementById('participantModal').style.display = 'none';
        }
    </script>
@endpush
