<style>
  .short-description {
      display: inline;
  }

  .full-description {
      display: none;
  }

  body {
      background-color: #f8f9fa;
  }

  .sidebar {
      width: 300px;
      height: 100vh;
      background: #fff;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
      padding: 20px;
  }

  .sidebar h6 {
      font-weight: bold;
      margin-top: 20px;
  }

  .sidebar .nav-link {
      color: #333;
      padding: 8px 15px;
      border-radius: 5px;
  }

  .sidebar .nav-link:hover {
      background-color: #f1f1f1;
  }

  .content {
      flex-grow: 1;
      padding: 20px;
  }

  .store-table {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  .btn-add {
      background-color: #5cb85c;
      color: white;
      border-radius: 5px;
      padding: 10px 15px;
      border: none;
  }

  * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
  }

  .dashboard {
      width: 90%;
      margin: 20px auto;
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 20px;
  }

  header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
  }

  header h1 {
      font-size: 24px;
      color: #333;
  }

  .add-btn {
      padding: 10px 15px;
      background-color: #6c63ff;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
  }

  table {
      width: 100%;
      border-collapse: collapse;
  }

  th,
  td {
      text-align: left;
      padding: 12px;
      border-bottom: 1px solid #ddd;
  }

  th {
      background-color: #f0f2f5;
      font-weight: bold;
      color: #333;
  }

  td {
      color: #555;
  }

  .action-btn {
      margin-right: 5px;
      padding: 5px 10px;
      background-color: #6c63ff;
      color: #fff;
      border: none;
      border-radius: 3px;
      cursor: pointer;
  }

  .action-btn:last-child {
      background-color: #ff4d4d;
  }

  .action-btn:hover {
      opacity: 0.9;
  }

  .button-group {
      display: flex;
      gap: 5px;
      /* Adjust spacing between buttons as needed */
  }


  .shadow-box {
      width: 200px;
      /* Adjust as needed */
      height: 100px;
      /* Adjust as needed */
      background-color: #f5f5f5;
      /* Placeholder background color */
      margin: 50px auto;
      position: relative;
      border: 1px solid #ddd;
      /* Optional border */
  }

  .shadow-box::after {
      content: "";
      position: absolute;
      bottom: -15px;
      /* Distance of shadow from the box */
      left: 50%;
      width: 70%;
      /* Width of the shadow */
      height: 20px;
      /* Height of the shadow */
      background: rgba(0, 0, 0, 0.2);
      /* Shadow color */
      border-radius: 50%;
      /* Curved effect */
      transform: translateX(-50%);
      z-index: -1;
      /* Make sure shadow is behind the box */
      filter: blur(8px);
      /* Optional: soften the shadow */
  }


  .tab-pane {
      padding: 20px;
      border-top: none;
  }



  .step.in-progress .circle {
      background-color: #673ab7;
      /* Purple for in-progress steps */
  }

  .label {
      margin-top: 5px;
      font-size: 12px;
      color: #555;
  }

  .line {
      flex-grow: 1;
      height: 2px;
      background-color: #ddd;
  }

  .line.completed {
      background-color: #4caf50;
      /* Green for completed lines */
  } */


</style>
