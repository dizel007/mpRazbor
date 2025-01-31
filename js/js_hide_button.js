function alerting(){

    var lock_1 = document.getElementById('up_input'); 
      if (lock_1) {
          lock_1.className = 'LockOn'; 
          // lock.innerHTML = str; 
      }
      var lock_2 = document.getElementById('down_input'); 
      
      if (lock_2) {
          lock_2.className = 'LockOn'; 
          // lock.innerHTML = str; 
      }

      var see_text = document.getElementById('OnLock_textLockPane'); 
      if (see_text) {
          see_text.className = 'LockOff'; 
          // lock.innerHTML = str; 
      }



  }


function myFunction() {
    // Import the mysql2 module
const mysql = require('mysql2');

// Create a connection to the database
const connection = mysql.createConnection({
  host: 'localhost',     // Replace with your host
  user: 'root',          // Replace with your username
  password: '',          // Replace with your password
  database: 'users_mp_razbor'       // Replace with your database name
});

// Connect to the database
connection.connect(error => {
  if (error) {
    console.error('Error connecting to the database:', error);
    return;
  }
  console.log('Connected to the database');
});

// Run a database query
connection.query('SELECT * FROM users', (error, results) => {
  if (error) {
    console.error('Error executing query:', error);
    return;
  }
  console.log('Query results:', results);
});

// Close the connection
connection.end();

    document.getElementById('formoid').action = 'start_new_supplies.php';
  alert('Вы нажали на кнопку!');
}
