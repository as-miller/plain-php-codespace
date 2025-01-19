<footer class="bg-light text-center py-3 mt-auto">
    <p>&copy; <?php echo date('Y'); ?> My Website. All rights reserved.</p>
</footer>

<!-- Essential libraries first -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Chat functionality -->
 <script>
$(document).ready(function() {
    // Group Chat Functionality
    const groupChatForm = $('#groupChatForm');
    const groupMessageInput = $('#groupMessage');
    const groupChatMessages = $('#groupChatMessages');

    // Handle group chat form submission
    groupChatForm.submit(function(event) {
        event.preventDefault();
        
        const message = groupMessageInput.val().trim();
        if (message === '') return;

        $.ajax({
            url: 'send_message.php',
            method: 'POST',
            data: {
                groupMessage: message
            },
            success: function(response) {
                groupMessageInput.val('');
                fetchGroupMessages();
            },
            error: function(xhr, status, error) {
                console.error('Error sending message:', error);
                alert('Failed to send message. Please try again.');
            }
        });
    });

    // Function to fetch group messages
    function fetchGroupMessages() {
        $.get('fetch_messages.php', function(data) {
            groupChatMessages.html(data);
            groupChatMessages.scrollTop(groupChatMessages[0].scrollHeight);
        }).fail(function(error) {
            console.error('Error fetching messages:', error);
        });
    }

    // Initialize group chat - load messages immediately and set up periodic refresh
    fetchGroupMessages(); // Initial load
    setInterval(fetchGroupMessages, 3000); // Refresh every 3 seconds

    // Private Messages Functionality
    const recipientSelect = $('#recipientSelect');
    const privateMessageForm = $('#privateMessageForm');
    const privateMessageInput = $('#privateMessage');
    const privateMessageList = $('#privateMessageList');
    let currentRecipientId = null;

    // Load private messages when a recipient is selected
    recipientSelect.change(function() {
        currentRecipientId = $(this).val();
        if (currentRecipientId) {
            loadPrivateMessages(currentRecipientId);
        }
    });

    // Function to format timestamp
    function formatTimestamp(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // Load private messages
    function loadPrivateMessages(recipientId) {
        $.ajax({
            url: 'get_private_messages.php',
            method: 'GET',
            data: { recipient_id: recipientId },
            dataType: 'json',
            success: function(messages) {
                privateMessageList.empty();
                if (Array.isArray(messages)) {
                    messages.forEach(message => {
                        const messageClass = message.is_sender ? 'text-end' : 'text-start';
                        const messageHtml = `
                            <div class="message mb-2 ${messageClass}">
                                <strong>${message.sender_username}:</strong> 
                                <span class="message-text">${message.message}</span>
                                <small class="text-muted ms-2">${formatTimestamp(message.timestamp)}</small>
                            </div>
                        `;
                        privateMessageList.append(messageHtml);
                    });
                    // Scroll to bottom after adding messages
                    privateMessageList.scrollTop(privateMessageList[0].scrollHeight);
                } else {
                    console.error('Invalid messages format:', messages);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading messages:', error);
                privateMessageList.html('<div class="alert alert-danger">Error loading messages</div>');
            }
        });
    }

    // Handle private message submission
    privateMessageForm.submit(function(e) {
        e.preventDefault();
        
        const message = privateMessageInput.val().trim();
        if (!currentRecipientId || !message) {
            alert('Please select a recipient and enter a message');
            return;
        }

        $.ajax({
            url: 'send_private_message.php',
            method: 'POST',
            data: {
                recipient_id: currentRecipientId,
                message: message
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    privateMessageInput.val('');
                    loadPrivateMessages(currentRecipientId);
                } else {
                    alert('Error: ' + (response.message || 'Failed to send message'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error sending message:', error);
                alert('Failed to send message. Please try again.');
            }
        });
    });

    // Set up periodic refresh of messages when tab is active
    let messageRefreshInterval;
    
    // Watch for tab changes
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (e.target.getAttribute('href') === '#private-messages') {
            // Start refreshing when private messages tab is active
            if (currentRecipientId) {
                loadPrivateMessages(currentRecipientId);
                messageRefreshInterval = setInterval(function() {
                    if (currentRecipientId) {
                        loadPrivateMessages(currentRecipientId);
                    }
                }, 3000);
            }
        } else {
            // Clear interval when leaving private messages tab
            clearInterval(messageRefreshInterval);
        }
    });

    // Add some debug logging
    console.log('Chat system initialized');
});
</script>

<!-- Enemy select functionality -->
<script>
$.get('get_enemies.php', function(data) {
    const enemies = JSON.parse(data);
    enemies.forEach(enemy => {
        $('#enemySelect').append(`<option value="${enemy.id}">${enemy.name}</option>`);
    });
});
</script>

<script>
      function rollDice() {
    const diceType = document.getElementById('diceType').value;
    const result = Math.floor(Math.random() * diceType) + 1;

    // Create log entry immediately for responsive UI
    const logEntry = document.createElement('div');
    logEntry.className = 'log-entry mb-2';
    const timestamp = new Date().toLocaleString();
    logEntry.innerHTML = `
        <span class="text-muted">[${timestamp}]</span>
        <span class="ms-2">d${diceType} roll:</span>
        <span class="fw-bold ms-2">${result}</span>
    `;
    
    const diceLog = document.getElementById('diceLog');
    diceLog.insertBefore(logEntry, diceLog.firstChild);

    // Save to database via AJAX
    fetch('includes/sidebar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=roll&diceType=${diceType}&result=${result}`
    })
    .then(response => {
        if (!response.ok) {
            console.error('Failed to save roll to database.');
        }
    })
    .catch(err => console.error('Error:', err));
}

function clearLog() {
    document.getElementById('diceLog').innerHTML = '';

    // Clear database via AJAX
    fetch('includes/sidebar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=clear'
    })
    .then(response => {
        if (!response.ok) {
            console.error('Failed to clear rolls from the database.');
        }
    })
    .catch(err => console.error('Error:', err));
}
    </script>


</body>
</html>