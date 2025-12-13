<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Campus Event Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
  <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>

  <style>
    body {
      background: linear-gradient(135deg, #eef2ff, #f8fafc);
      min-height: 100vh;
      font-family: system-ui, sans-serif;
    }
    .header {
      background: linear-gradient(90deg, #6366f1, #7c3aed);
      color: white;
      padding: 30px;
      border-radius: 20px;
      margin-bottom: 20px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0,0,0,.15);
    }
    .nav-btn {
      margin-right: 8px;
      border-radius: 999px;
    }
    .card-box {
      background: white;
      border-radius: 20px;
      padding: 25px;
      box-shadow: 0 10px 30px rgba(0,0,0,.1);
    }
  </style>
</head>

<body>
<div id="root"></div>

<script>
const { useState, useEffect } = React;

function App() {
  const [page, setPage] = useState("home");
  const [user, setUser] = useState(null);
  const [events, setEvents] = useState([]);
  const [msg, setMsg] = useState("");

  useEffect(() => {
    fetch("auth_me.php")
      .then(r => r.json())
      .then(d => setUser(d.user || null));

    fetch("events.php")
      .then(r => r.json())
      .then(d => setEvents(d.events || []));
  }, []);

  function logout() {
    fetch("auth_logout.php")
      .then(() => {
        setUser(null);
        setPage("home");
      });
  }

  function login(email, password) {
    const fd = new FormData();
    fd.append("email", email);
    fd.append("password", password);

    fetch("auth_login.php", { method: "POST", body: fd })
      .then(r => r.json())
      .then(d => {
        if (d.error) setMsg(d.error);
        else {
          setUser(d.user);
          setPage("home");
          setMsg("");
        }
      });
  }

  function signup(name, email, password) {
    const fd = new FormData();
    fd.append("name", name);
    fd.append("email", email);
    fd.append("password", password);

    fetch("auth_register.php", { method: "POST", body: fd })
      .then(r => r.json())
      .then(d => {
        if (d.error) setMsg(d.error);
        else {
          setUser(d.user);
          setPage("home");
          setMsg("");
        }
      });
  }

  return React.createElement("div", { className: "container mt-4" }, [

    React.createElement("div", { className: "header", key: "h" },
      "Campus Event Management"
    ),

    React.createElement("div", { key: "nav", className: "mb-3" }, [
      btn("Home", () => setPage("home"), "primary"),
      btn("Events", () => setPage("events"), "secondary"),
      btn("Calendar", () => setPage("calendar"), "secondary"),

      !user && btn("Login", () => setPage("login"), "success"),
      !user && btn("Signup", () => setPage("signup"), "primary"),

      user && (user.role === "organizer" || user.role === "admin") &&
        btn("Create Event", () => setPage("create"), "success"),

      user && btn("Logout", logout, "danger")
    ]),

    msg && React.createElement("div", { className: "alert alert-info", key: "m" }, msg),

    page === "home" && card("Welcome", user ? `Logged in as ${user.name} (${user.role})` : "Please login or signup."),
    page === "events" && card("Events",
      events.length ? events.map(e => `${e.title} @ ${e.location}`) : "No events yet"
    ),
    page === "calendar" && card("Calendar", "Coming soon"),
    page === "login" && Login({ onLogin: login }),
    page === "signup" && Signup({ onSignup: signup }),
    page === "create" && CreateEvent()
  ]);
}

function btn(text, onClick, color) {
  return React.createElement("button", {
    className: `btn btn-${color} nav-btn`,
    onClick
  }, text);
}

function card(title, content) {
  return React.createElement("div", { className: "card-box" }, [
    React.createElement("h3", null, title),
    Array.isArray(content)
      ? content.map((c,i)=>React.createElement("p",{key:i},c))
      : React.createElement("p", null, content)
  ]);
}

function Login({ onLogin }) {
  let email, password;
  return card("Login",
    React.createElement("div", null, [
      input("Email", v => email = v),
      input("Password", v => password = v, "password"),
      btn("Login", () => onLogin(email, password), "success")
    ])
  );
}

function Signup({ onSignup }) {
  let name, email, password;
  return card("Signup",
    React.createElement("div", null, [
      input("Name", v => name = v),
      input("Email", v => email = v),
      input("Password", v => password = v, "password"),
      btn("Signup", () => onSignup(name, email, password), "primary")
    ])
  );
}

function CreateEvent() {
  return card("Create Event", "Event creation backend already works.");
}

function input(ph, cb, type="text") {
  return React.createElement("input", {
    className: "form-control mb-2",
    placeholder: ph,
    type,
    onChange: e => cb(e.target.value)
  });
}

ReactDOM.createRoot(document.getElementById("root")).render(
  React.createElement(App)
);
</script>
</body>
</html>
